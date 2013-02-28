<?php
/**
 * Google Visualization API Query Parser and Database Layer
 *
 * LICENSE
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 *
 * @package    MC_Google_Visualization
 * @author     Chadwick Morris <chad@mailchimp.com>
 * @license    http://www.opensource.org/licenses/mit-license.php
 * @version    0.1
 */

require_once 'MC/Parser.php';

/**
 * Add a few custom exception to be used for error handling in the system
 */
class MC_Google_Visualization_Error extends Exception {
    public $type = 'server_error';
    public $summary = 'Server Error';
}
class MC_Google_Visualization_QueryError extends MC_Google_Visualization_Error {
    public $type = 'invalid_query';
    public $summary = 'Invalid Query';
}

/**
 * Provide a working implementation of the Google Visualization Query data source that works with a database (or any other custom backend)
 * The documentation for the query language itself and how to use it with Google Visualizations can be found here: http://code.google.com/apis/visualization/documentation/querylanguage.html
 */
class MC_Google_Visualization {
    /**
     * The default entity that will be used if the "from" part of a query is left out.  Setting this to null
     * will make a "from" clause required
     * @var string
     */
    protected $default_entity = null;
    
    /**
     * The entity schema that defines which tables are exposed to visualization clients, along with their fields, joins, and callbacks
     * @var array
     */
    protected $entities = array();
    
    /**
     * If pivots are being used or MC_Google_Visualization is handling the whole request, this must be a PDO
     * connection to your database.
     * @var PDO
     */
    protected $db = null;
    
    /**
     * The SQL dialect to use when auto-generating SQL statements from the parsed query tokens
     * defaults to "mysql".  Allowed values are "mysql", "postgres", or "sqlite".  Patches are welcome for the rest.
     * @var string
     */
    protected $sql_dialect = 'mysql';

    /**
     * If a format string is not provided by the query, these will be used to format values by default.
     * @var array
     */
    protected $default_format = array(
        'date' => 'm/d/Y',
        'datetime' => 'm/d/Y h:ia',
        'time' => 'h:ia',
        'boolean' => 'FALSE:TRUE',
        'number' => 'num:0'
    );
    
    /**
     * The current supported version of the Data Source protocol
     * @var float
     */
    protected $version = 0.5;

    /**
     * Create a new instance.  This must be done before the library can be used.  Pass in a PDO connection and
     * dialect if MC_Google_Visualization will handle the entire request cycle
     * @param PDO $db the database connection to use
     * @param string $dialect the SQL dialect to use - one of "mysql", "postgres", or "sqlite"
     */
    public function __construct($db=null, $dialect='mysql') {
        if(!class_exists('Zend_Json') && !function_exists('json_encode')) {
            throw new MC_Google_Visualization_Error('You must include either the Zend JSON library or have the PHP json extension installed to use the MC Google Visualization Server');
        }

        $this->setDB($db);
        $this->setSqlDialect($dialect);
    }
    
    /**
     * Set the database connection to use when handling the entire request or getting pivot values
     * @param PDO|null $db the database connection to use - or null if you want to handle your own queries
     */
    public function setDB($db=null) {
        if($db !== null && !($db instanceof PDO)) {
            throw new MC_Google_Visualization_Error('You must give a PDO database connection');
        } elseif($db !== null) {
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        
        $this->db = $db;
    }
    
    /**
     * Set the dialect to use when generating SQL statements
     * @param string $dialect one of "mysql", "postgres", or "sqlite"
     */
    public function setSqlDialect($dialect) {
        if($dialect != 'mysql' && $dialect != 'postgres' && $dialect != 'sqlite') {
            throw new MC_Google_Visualization_Error('SQL dialects must be one of "mysql", "postgres", or "sqlite" - not "' . $dialect . '"');
        }
        
        $this->sql_dialect = $dialect;
    }

    /**
     * Change the default format string to use for a particular data type
     * @param string $type the data type to change - one of "date", "datetime", "time", "boolean", or "number"
     * @param string $format the format string to use for the data type
     */
    public function setDefaultFormat($type, $format) {
        if(!isset($this->default_format[$type])) throw new MC_Google_Visualization_Error('Unknown or unformattable type: "' . $type . '"');
        if($type == 'boolean' && strpos($format, ':') === false) throw new MC_Google_Visualization_Error('Invalid boolean format string: "' . $format . '"');
        $this->default_format[$type] = $format;
    }

    /**
     * Handle the entire request, pulling the query from the $_GET variables, and printing the results directly
     */
    public function handleRequest() {
        $query = isset($_GET['tq'])?$_GET['tq']:'';
        $params = array('version' => $this->version, 'responseHandler' => 'google.visualization.Query.setResponse');
        $paramlist = explode(';', $_GET['tqx']);
        foreach($paramlist as $paramstr) {
            list($name, $val) = explode(':', $paramstr);
            $params[$name] = $val;
        }
        
        $params['reqId'] = (int) $params['reqId'];
        $params['version'] = (float) $params['version'];
        if($params['version'] > $this->version) {
            throw new MC_Google_Visualization_Error('Data Source version ' . $params['version'] . ' is unsupported at this time');
        }
        
        if(isset($_GET['responseHandler'])) {
            $params['responseHandler'] = $_GET['responseHandler'];
        }

        $this->handleQuery($query, $params);
    }

    /**
     * Handle a specific query.  Use this if you want to gather the query parameters yourself instead of using handleRequest()
     * @param string $query the visualization query to parse and execute
     * @param array $params all extra params sent along with the query - must include at least "reqId" key
     */
    public function handleQuery($query, $params) {
        try {
            if(!($this->db instanceof PDO)) {
                throw new MC_Google_Visualization_Error('You must pass a PDO connection to the MC Google Visualization Server if you want to let the server handle the entire request');
            }

            $reqid = $params['reqId'];
            $query = $this->parseQuery($query);
            $meta = $this->generateMetadata($query);
            $sql = $this->generateSQL($meta);
            $meta['req_id'] = $reqid;
            $meta['req_params'] = $params;

            $stmt = $this->db->query($sql);
            //If we got here, there's no errors
            echo $this->getSuccessInit($meta);
            $first = true;
            foreach($stmt as $row) {
                if(!$first) echo ',';
                echo $this->getRowValues($row, $meta);
                $first = false;
            }
            echo $this->getSuccessClose();

            $stmt = null;
        } catch(MC_Google_Visualization_Error $e) {
            echo $this->handleError($reqid, $e->getMessage(), $params['responseHandler'], $e->type, $e->summary);
        } catch(PDOException $e) {
            echo $this->handleError($reqid, $e->getMessage(), $params['responseHandler'], 'invalid_query', 'Invalid Query');
        } catch(MC_Parser_ParseError $e) {
            echo $this->handleError($reqid, $e->getMessage(), $params['responseHandler'], 'invalid_query', 'Invalid Query');
        } catch(Exception $e) {
            echo $this->handleError($reqid, $e->getMessage(), $params['responseHandler']);
        }
    }

    /**
     * Return the response appropriate to tell the visualization client that an error has occurred
     * @param integer $reqid the request ID that caused the error
     * @param string $detail_msg the detailed message to send along with the error
     * @param string $code the code for the error (like "error", "server_error", "invalid_query", "access_denied", etc.)
     * @param string $summary_msg a short description of the error, appropriate to show to end users
     * @return string the string to output that will cause the visualization client to detect an error
     */
    public function handleError($reqid, $detail_msg, $handler='google.visualization.Query.setResponse', $code='error', $summary_msg=null) {
        if($summary_msg === null) $summary_msg = $detail_msg;
        $handler = ($handler) ? $handler : 'google.visualization.Query.setResponse';
        return $handler . '({version:"' . $this->version . '",reqId:"' . $reqid . '",status:"error",errors:[{reason:' . $this->jsonEncode($code) . ',message:' . $this->jsonEncode($summary_msg) . ',detailed_message:' . $this->jsonEncode($detail_msg) . '}]});';
    }

    /**
     * Given the metadata for a query and the entities it's working against, generate the SQL
     * @param array $meta the results of generateMetadata() on the parsed visualization query
     * @return string the SQL version of the visualization query
     */
    public function generateSQL(&$meta) {
        if(!isset($meta['query_fields'])) $meta['query_fields'] = $meta['select'];
        
        if(isset($meta['pivot'])) {
            //Pivot queries are special - they require an entity to be passed and modify the query directly
            $entity = $meta['entity'];
            $pivot_fields = array();
            $pivot_joins = array();
            $pivot_group = array();
            foreach($meta['pivot'] as $entity_field) {
                $field = $entity['fields'][$entity_field];
                if(isset($field['callback'])) throw new MC_Google_Visualization_QueryError('Callback fields cannot be used as pivots: "' . $entity_field .'"');
                $pivot_fields[] = $field['field'] . ' AS ' . $entity_field;
                $pivot_group[] = $entity_field;
                if($field['join'] && !in_array($entity['joins'][$field['join']], $pivot_joins)) $pivot_joins[] = $entity['joins'][$field['join']];
            }

            $pivot_sql = 'SELECT ' . implode(', ', $pivot_fields) . ' FROM ' . $meta['table'];
            if(!empty($pivot_joins)) {
                $pivot_sql .= ' ' . implode(' ', $pivot_joins);
            }
            $pivot_sql .= ' GROUP BY ' . implode(', ', $pivot_group);

            $func_fields = array();
            $new_fields = array();
            foreach($meta['query_fields'] as $field) {
                if(is_array($field)) {
                    $func_fields[] = $field;
                } else {
                    $new_fields[] = $field;
                }
            }
            $meta['query_fields'] = $new_fields;

            $stmt = $this->db->query($pivot_sql);
            foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                //Create a version of all function-ed fields for each unique combination of pivot values
                foreach($func_fields as $field) {
                    $field[2] = $row;

                    $meta['query_fields'][] = $field;
                }
            }

            $stmt = null;
            //For pivot queries, the fields we return and the fields we query against are always the same
            $meta['select'] = $meta['query_fields'];
        }

        $query_sql = array();
        $join_sql = $meta['joins'];
		$pivot_cond = null;
        foreach($meta['query_fields'] as $field) {
            $func = null;
            if(is_array($field)) {
                $func = $field[0];
                $pivot_cond = (isset($field[2])) ? $field[2] : null;
                $field = $field[1];
            }
            $query_sql[] = $this->getFieldSQL($field, $meta['field_spec'][$field], true, $func, $pivot_cond, $meta['field_spec']);
        }

        if(isset($meta['where'])) {
            $where_str = array();
            foreach($meta['where'] as &$where_part) {
                //Replace field references with their SQL forms
                switch($where_part['type']) {
                    case 'where_field':
                        $where_part['value'] = $this->getFieldSQL($where_part['value'], $meta['field_spec'][$where_part['value']]);
                        break;
                    case 'datetime':
                    case 'timestamp':
                        $where_part['value'] = $this->convertDateTime(trim($where_part['value'][1], '\'"'));
                        break;
                    case 'timeofday':
                        $where_part['value'] = $this->convertTime(trim($where_part['value'][1], '\'"'));
                        break;
                    case 'date':
                        $where_part['value'] = $this->convertDate(trim($where_part['value'][1], '\'"'));
                        break;
                    case 'null':
                    case 'notnull':
                        $where_part['value'] = strtoupper(implode(' ', $where_part['value']));
                        break;
                }
                
                $where_str[] = $where_part['value'];
            }
            $where_str = implode(' ', $where_str);
        }

        $sql = 'SELECT ' . implode(', ', $query_sql) . ' FROM ' . $meta['table'];
        if(!empty($join_sql)) {
            $sql .= ' ' . implode(' ', $join_sql);
        }

        if( (isset($where_str)&&$where_str) || isset($meta['global_where'])) {
            if(!isset($where_str) || !$where_str) $where_str = '1=1';
            $sql .= ' WHERE (' . $where_str . ')';
            if(isset($meta['global_where'])) $sql .= ' AND ' . $meta['global_where'];
        }

        if(isset($meta['groupby'])) {
            $group_sql = array();
            foreach($meta['groupby'] as $group) {
                $group_sql[] = $this->getFieldSQL($group, $meta['field_spec'][$group]);
            }
            $sql .= ' GROUP BY ' . implode(', ', $group_sql);
        }

        if(isset($meta['orderby'])) {
            $sql .= ' ORDER BY';
            $first = true;
            foreach($meta['orderby'] as $field => $dir) {
                if(isset($meta['field_spec'][$field]['sort_field'])) {
                    //An entity field can delegate sorting to another field by using the "sort_field" key
                    $field = $meta['field_spec'][$field]['sort_field'];
                }
                $spec = $meta['field_spec'][$field];
                if(!$first) $sql .= ',';
                
                $sql .= ' ' . $this->getFieldSQL($field, $spec) . ' ' . strtoupper($dir);
                $first = false;
            }
        }

        if(isset($meta['limit']) || isset($meta['offset'])) {
            $sql .= $this->convertLimit($meta['limit'], $meta['offset']);
        }

        return $sql;
    }
    
    /**
     * Convert a visualization date into the appropriate date-literal format for the SQL dialect
     * @param string $value the date as a string "YYY-MM-DD"
     * @return string the same value converted to be used inline in a SQL query
     */
    protected function convertDate($value) {
        return "'" . $value . "'";
    }
    
    /**
     * Convert a visualization date/time into the appropriate literal format for the SQL dialect
     * @param string $value the date/time as a string "YYY-MM-DD HH:NN:SS"
     * @return string the same value converted to be used inline in a SQL query
     */
    protected function convertDateTime($value) {
        return "'" . $value . "'";
    }
    
    /**
     * Convert a visualization time into the appropriate literal format for the SQL dialect
     * @param string $value the time as a string "HH:NN:SS"
     * @return string the same value converted to be used inline in a SQL query
     */
    protected function convertTime($value) {
        return "'" . $value . "'";
    }
    
    /**
     * Convert the limit and offset clauses from the visualization query into SQL
     * @param integer|null $limit the limit value, or null if not provided
     * @param integer|null $offset the offset value, or null if not provided
     * @return string the limit clause converted to be used inline in a SQL query
     */
    protected function convertLimit($limit, $offset) {
        $sql = '';
        if($limit !== null) $sql .= ' LIMIT ' . $limit;
        if($offset !== null) $sql .= ' OFFSET ' . $offset;
        return $sql;
    }
    
    /**
     * Return the character used to quote aliases for this query SQL dialect
     * $return string the quote character
     */
    protected function getFieldQuote() {
        switch($this->sql_dialect) {
            case 'postgres':
                return '"';
            default:
                return '`';
        }
    }
    
    /**
     * Helper function to generate the SQL for a given entity field
     * @param string $name the name of the field to generate SQL for
     * @param array $spec the entity spec array for the field
     * @param boolean $alias whether to also generate an "AS" alias for the field - defaults to false
     * @param string|null $func the function to call against the field (count, avg, sum, max, min)
     * @param array|null $pivot if there was a pivot for this query, this should be an array of values that uniquely identify this field
     * @param array|null $pivot_fields if there was a pivot for this query, this should be an array of the specs for the pivoted fields
     * @return string the SQL string for this field, with an op
     */
    protected function getFieldSQL($name, $spec, $alias=false, $func=null, $pivot=null, $pivot_fields=null) {
        $sql = $spec['field'];
        $q = $this->getFieldQuote();
        if($func !== null) {
            if($pivot === null) {
                $sql = strtoupper($func) . '(' . $sql . ')';
                if($alias) $sql .= ' AS ' . $q . $func . '-' . $name . $q;
            } else {
                $casewhen = array();
                foreach($pivot as $key => $val) {
                    $pivot_field = $pivot_fields[$key];
                    $casewhen[] = $pivot_field['field'] . '=' . $this->db->quote($val);
                }
                $sql = strtoupper($func) . '(CASE WHEN ' . implode(' AND ', $casewhen) . ' THEN ' . $sql . ' ELSE NULL END)';
                if($alias) $sql .= ' AS ' . $q . implode(',', $pivot) . ' ' . $func . '-' . $name . $q;
            }
        } elseif($alias) {
            $sql .= ' AS ' . $name;
        }
        
        return $sql;
    }
    
    /**
     * Given the results of parseQuery(), introspect against the entity definitions provided and return the metadata array used to generate the SQL
     * @param array $query the visualization query broken up into sections
     * @return array the metadata array from merging the query with the entity table definitions
     */
    public function generateMetadata($query) {
        $meta = array();
        if(!isset($query['from']) && $this->default_entity === null) {
            throw new MC_Google_Visualization_Error('FROM clauses are required if no default entity is defined');
        } elseif(!isset($query['from'])) {
            $query['from'] = $this->default_entity;
        }
        
        if(!isset($this->entities[$query['from']])) {
            throw new MC_Google_Visualization_QueryError('Unknown table "' . $query['from'] . '"');
        }
        
        $meta['entity_name'] = $query['from'];
        $entity = $this->entities[$query['from']];
        $meta['entity'] = $entity;
        $meta['table'] = $entity['table'];
        if(isset($entity['where'])) $meta['global_where'] = $entity['where'];
        
        if(!isset($query['select'])) {
            //By default, return all fields defined for an entity
            $query['select'] = array_keys($entity['fields']);
        }
        
        //The query fields might be different from the "select" fields (callback dependant fields will not be returned)
        $meta['query_fields'] = array();
        $meta['joins'] = array();
        $meta['field_spec'] = array();
        
        $field_meta = array();
        foreach($query['select'] as $sfield) {
            if(is_array($sfield)) {
                $field = $sfield[1];
            } else {
                $field = $sfield;
            }
            
            if(!isset($entity['fields'][$field])) {
                throw new MC_Google_Visualization_QueryError('Unknown column "' . $field . '"');
            }
            
            $field_spec = $entity['fields'][$field];
            if(isset($field_spec['join']) && !isset($meta['joins'][$field_spec['join']])) {
                $meta['joins'][$field_spec['join']] = $entity['joins'][$field_spec['join']];
            }
            
            if(isset($field_spec['callback'])) {
                if(isset($meta['pivot'])) {
                    throw new MC_Google_Visualization_QueryError('Callback-based fields cannot be used in pivot queries');
                }
                
                if(is_array($sfield)) {
                    throw new MC_Google_Visualization_Error('Callback-based fields cannot have functions called on them');
                }
                
                $this->addDependantCallbackFields($field_spec, $entity, $meta);
            } elseif(!in_array($sfield, $meta['query_fields'])) {
                $meta['query_fields'][] = $sfield;
            }
            
            $meta['field_spec'][$field] = $field_spec;
        }
        
        $meta['select'] = $query['select'];
        
        if(isset($query['where'])) {
            //Parse the where clauses and error out on non-existant and callback fields and add joins
            foreach($query['where'] as $where_token) {
                if($where_token['type'] == 'where_field') {
                    $field = $where_token['value'];
                    if(!isset($entity['fields'][$field])) {
                        throw new MC_Google_Visualization_QueryError('Unknown column in WHERE clause "' . $field . '"');
                    } elseif(isset($entity['fields'][$field]['callback'])) {
                        throw new MC_Google_Visualization_QueryError('Callback fields cannot be included in WHERE clauses');
                    }
                    
                    $field_spec = $entity['fields'][$field];
                    if(isset($field_spec['join']) && !isset($meta['joins'][$field_spec['join']])) {
                        $meta['joins'][$field_spec['join']] = $entity['joins'][$field_spec['join']];
                    }
                    
                    $meta['field_spec'][$field] = $field_spec;
                }
            }
        }
        
        //Also add the joins & field spec information for the orderby, groupby, and pivot clauses
        if(isset($query['pivot'])) {
            foreach($query['pivot'] as $field) {
                if(!isset($entity['fields'][$field])) {
                    throw new MC_Google_Visualization_QueryError('Unknown column in PIVOT clause "' . $field . '"');
                }
                
                $field_spec = $entity['fields'][$field];
                if(isset($field_spec['join']) && !isset($meta['joins'][$field_spec['join']])) {
                    $meta['joins'][$field_spec['join']] = $entity['joins'][$field_spec['join']];
                }
                $meta['field_spec'][$field] = $field_spec;
            }
        }
        
        if(isset($query['groupby'])) {
            foreach($query['groupby'] as $field) {
                if(!isset($entity['fields'][$field])) {
                    throw new MC_Google_Visualization_QueryError('Unknown column in GROUP BY clause "' . $field . '"');
                }
                
                $field_spec = $entity['fields'][$field];
                
                if(isset($field_spec['callback'])) {
                    throw new MC_Google_Visualization_QueryError('Callback-based fields cannot be used in GROUP BY clauses');
                }
                
                if(isset($field_spec['join']) && !isset($meta['joins'][$field_spec['join']])) {
                    $meta['joins'][$field_spec['join']] = $entity['joins'][$field_spec['join']];
                }
                $meta['field_spec'][$field] = $field_spec;
            }
        }
        
        if(isset($query['orderby'])) {
            foreach($query['orderby'] as $field => $dir) {
                if(!isset($entity['fields'][$field])) {
                    throw new MC_Google_Visualization_QueryError('Unknown column in ORDER BY clause "' . $field . '"');
                }
                
                $field_spec = $entity['fields'][$field];
                $meta['field_spec'][$field] = $field_spec;
                
                if(isset($field_spec['sort_field'])) {
                    $field = $field_spec['sort_field'];
                    $field_spec = $entity['fields'][$field_spec['sort_field']];
                }
                
                if(isset($field_spec['callback'])) {
                    throw new MC_Google_Visualization_QueryError('Callback-based fields cannot be used in ORDER BY clauses');
                }
                
                if(isset($field_spec['join']) && !isset($meta['joins'][$field_spec['join']])) {
                    $meta['joins'][$field_spec['join']] = $entity['joins'][$field_spec['join']];
                }
                $meta['field_spec'][$field] = $field_spec;
            }
        }
        
        //Some of the query information we just copy into the metadata array
        $copy_keys = array('where', 'orderby', 'groupby', 'pivot', 'limit', 'offset', 'labels', 'formats', 'options');
        foreach($copy_keys as $copy_key) {
            if(isset($query[$copy_key])) $meta[$copy_key] = $query[$copy_key];
        }
        return $meta;
    }
    
    /**
     * Recursively process the dependant fields for callback entity fields
     * @param array $field the spec array for the field to add (must have a "callback" key)
     * @param array $entity the spec array for the entity to pull other fields from
     * @param array $meta the query metadata array to append the results
     */
    protected function addDependantCallbackFields($field, $entity, &$meta) {
        foreach($field['fields'] as $dependant) {
            if(!isset($entity['fields'][$dependant])) {
                throw new MC_Google_Visualization_Error('Unknown callback required field "' . $dependant . '"');
            }
            
            $dependant_field = $entity['fields'][$dependant];
            $meta['field_spec'][$dependant] = $dependant_field;
            if(isset($dependant_field['callback'])) {
                $this->addDependantCallbackFields($dependant_field, $entity, $meta);
            } elseif(!in_array($dependant, $meta['query_fields'])) {
                if(isset($dependant_field['join']) && !isset($meta['joins'][$dependant_field['join']])) {
                    $meta['joins'][$dependant_field['join']] = $entity['joins'][$dependant_field['join']];
                }
                $meta['query_fields'][] = $dependant;
            }
        }
    }

    /**
     * Helper method for the query parser to recursively scan the delimited list of select fields
     * @param MC_Parser_Token $token the token or token group to recursively parse
     * @param array $fields the collector array reference to receive the flattened select field values
     */
    protected function parseFieldTokens($token, &$fields) {
        if($token->value == '*') {
            return;
        }

        if(!is_array($fields)) $fields = array();

        if($token->hasChildren()) {
            if($token->name == 'function') {
                $field = $token->getValues();
                $field[0] = strtolower($field[0]);
                $fields[] = $field;
            } else {
                foreach($token->getChildren() as $field) {
                    $this->parseFieldTokens($field, $fields);
                }
            }
        } else {
            $fields[] = $token->value;
        }
    }
    
    /**
     * Helper method for the query parser to recursively scan and flatten the where clause's conditions
     * @param MC_Parser_Token $token the token or token group to parse
     * @param array $where the collector array of tokens that make up the where clause
     */
    protected function parseWhereTokens($token, &$where) {
        if(!is_array($where)) $where = array();
        if($token->hasChildren()) {
            if($token->name) {
                $where[] = array('type' => $token->name, 'value' => $token->getValues());
            } else {
                foreach($token->getChildren() as $child) {
                    $this->parseWhereTokens($child, $where);
                }
            }
        } elseif($token->name) {
            $where[] = array('type' => $token->name, 'value' => $token->value);
        }
    }

    /**
     * Parse the query according to the visualization query grammar, and break down the constituent parts
     * @param string $str the query string to parse
     * @return array the parsed query as an array, keyed by each part of the query (select, from, where, groupby, pivot, orderby, limit, offset, label, format, options
     */
    public function parseQuery($str) {
        $query = array();
        $tokens = $this->getGrammar()->parse($str);

        foreach($tokens->getChildren() as $token) {
            switch($token->name) {
                case 'select':
                    $sfields = $token->getChildren();
                    $sfields = $sfields[1];

                    $this->parseFieldTokens($sfields, $fields);
                    $query['select'] = $fields;
                    break;
                case 'from':
                    $vals = $token->getValues();
                    $query['from'] = $vals[1];
                    break;
                case 'where':
                    $where_tokens = $token->getChildren();
                    $where_tokens = $where_tokens[1];
                    $this->parseWhereTokens($where_tokens, $where);
                    $query['where'] = $where;
                    break;
                case 'groupby':
                    $groupby = $token->getValues();
                    array_shift($groupby);
                    array_shift($groupby);
                    $query['groupby'] = $groupby;
                    break;
                case 'pivot':
                    if(!$this->db) throw new MC_Google_Visualization_QueryError('Pivots require a PDO database connection');
                    $pivot = $token->getValues();
                    array_shift($pivot);
                    $query['pivot'] = $pivot;
                    break;
                case 'orderby':
                    $orderby = $token->getValues();
                    array_shift($orderby);
                    array_shift($orderby);
                    $field_dir = array();
                    $order_cnt = count($orderby);
                    for($i=0; $i<$order_cnt; ++$i) {
                        $field = $orderby[$i];
                        $dir = strtolower($orderby[$i + 1]);
                        if($dir == 'asc' || $dir == 'desc') {
                            ++$i;
                        } else {
                            $dir = 'asc';
                        }
                        $field_dir[$field] = $dir;
                    }
                    $query['orderby'] = $field_dir;
                    break;
                case 'limit':
                    $limit = $token->getValues();
                    $limit = $limit[1];
                    $query['limit'] = $limit;
                    break;
                case 'offset':
                    $offset = $token->getValues();
                    $offset = $offset[1];
                    $query['offset'] = $offset;
                    break;
                case 'label':
                    $labels = $token->getValues();
                    array_shift($labels);

                    $query_labels = array();
                    for($i=0; $i<count($labels); $i += 2) {
                        $field = $labels[$i];
                        $label = trim($labels[$i + 1], '\'"');
                        $query_labels[$field] = $label;
                    }
                    $query['labels'] = $query_labels;
                    break;
                case 'format':
                    $formats = $token->getValues();
                    array_shift($formats);

                    $query_formats = array();
                    for($i=0; $i<count($formats); $i += 2) {
                        $field = $formats[$i];
                        $formatstr = trim($formats[$i + 1], '\'"');

                        if($entity['fields'][$field]['type'] == 'boolean' && strpos($formatstr, ':') === false) {
                            throw new MC_Google_Visualization_QueryError('Invalid boolean format string: "' . $formatstr . '"');
                        }

                        $query_formats[$field] = $formatstr;
                    }
                    $query['formats'] = $query_formats;
                    break;
                case 'options':
                    $qoptions = $token->getValues();
                    array_shift($qoptions);
                    $options = array();
                    foreach($qoptions as $option) {
                        $options[$option] = true;
                    }
                    $query['options'] = $options;
                    break;
                default:
                    throw new MC_Google_Visualization_QueryError('Unknown query clause "' . $token->name . '"');
            }
        }
        
        return $query;

    }

    /**
     * Add a new entity (table) to the visualization server that maps onto one or more SQL database tables
     * @param string $name the name of the entity - should be used in the "from" clause of visualization queries
     * @param array $spec optional spec array with keys "fields", "joins", "table", and "where" to define the mapping between visualization queries and SQL queries
     */
    public function addEntity($name, $spec=array()) {
        $entity = array('table' => ($spec['table']) ? $spec['table'] : $name, 'fields' => array(), 'joins' => array());
        $this->entities[$name] = $entity;
        
        if(isset($spec['fields'])) {
            foreach($spec['fields'] as $field_name => $field_spec) {
                $this->addEntityField($name, $field_name, $field_spec);
            }
        }
        
        if(isset($spec['joins'])) {
            foreach($spec['joins'] as $join_name => $join_sql) {
                $this->addEntityJoin($name, $join_name, $join_sql);
            }
        }
        
        if(isset($spec['where'])) {
            $this->setEntityWhere($name, $spec['where']);
        }
    }
    
    /**
     * Add a new field to an entity table
     * @param string $entity the name of the entity to add the field to
     * @param string $field the name of the field
     * @param array $spec the metadata for the field as a set of key-value pairs - allowed keys are "field", "callback", "fields", "extra", "sort_field", "type", and "join"
     */
    public function addEntityField($entity, $field, $spec) {
        if(!isset($spec['field']) && !isset($spec['callback'])) {
            throw new MC_Google_Visualization_Error('Entity fields must either be mapped to database fields or given callback functions');
        }
        
        if(!isset($this->entities[$entity])) {
            throw new MC_Google_Visualization_Error('No entity table defined with name "' . $entity . '"');
        }
        
        if(!isset($spec['callback']) && (isset($spec['fields']) || isset($spec['extra']))) {
            throw new MC_Google_Visualization_Error('"fields" and "extra" parameters only apply to callback fields');
        }
        
        $this->entities[$entity]['fields'][$field] = $spec;
    }
    
    /**
     * Add a new optional join to the entity table.  If fields associated with this join are selected, the join will be added to the SQL query
     * @param string $entity the name of the entity table to add the join to
     * @param string $join the name of the join.  Set the entity field's "join" key to this
     * @param string $sql the SQL for the join that will be injected into the query
     */
    public function addEntityJoin($entity, $join, $sql) {
        if(!isset($this->entities[$entity])) {
            throw new MC_Google_Visualization_Error('No entity table defined with name "' . $entity . '"');
        }
        
        $this->entities[$entity]['joins'][$join] = $sql;
    }
    
    /**
     * Add a particular "WHERE" clause to all queries against an entity table
     * @param string $entity the name of the entity to add the filter to
     * @param string $where the SQL WHERE condition to add to all queries against $entity
     */
    public function setEntityWhere($entity, $where) {
        if(!isset($this->entities[$entity])) {
            throw new MC_Google_Visualization_Error('No entity table defined with name "' . $entity . '"');
        }
        
        $this->entities[$entity]['where'] = $where;
    }
    

    /**
     * Set the default entity to be used when a "from" clause is omitted from a query.  Set to null to require a "from" clause for all queries
     * @param string|null $default the new default entity
     */
    public function setDefaultEntity($default=null) {
        if($default !== null && !isset($this->entities[$default])) {
            throw new MC_Google_Visualization_Error('No entity exists with name "' . $default . '"');
        }

        $this->default_entity = $default;
    }

    /**
     * Return the beginning of a visualization response from the query metadata (everything before the actual row data)
     * @param array $meta the metadata for the query - generally generated by MC_Google_Visualization::generateMetadata
     * @return string the initial output string for a successful query
     */
    public function getSuccessInit($meta) {
        $handler = ($meta['req_params']['responseHandler']) ? $meta['req_params']['responseHandler'] : 'google.visualization.Query.setResponse';
        $version = ($meta['req_params']['version']) ? $meta['req_params']['version'] : $this->version;
        return $handler . "({version:'" . $version . "',reqId:'" . $meta['req_id'] . "',status:'ok',table:" . $this->getTableInit($meta);
    }

    /**
     * Return the table metadata section of the visualization response for a successful query
     * @param array $meta the metadata for the query - generally generated by MC_Google_Visualization::generateMetadata
     */
    public function getTableInit($meta) {
        $field_init = array();
        foreach($meta['select'] as $field) {
            if(is_array($field)) {
                $function = $field[0];
                if(!isset($field[2])) {
                    $field_id = $function . '-' . $field[1];
                } else {
                    $field_id = implode(',', $field[2]) . ' ' . $function . '-' . $field[1];
                }
                $field = $field[1];
            } else {
                $function = null;
                $field_id = $field;
            }

            $label = (isset($meta['labels'][$field])) ? $meta['labels'][$field] : $field_id;
            $type = (isset($meta['field_spec'][$field]['type'])) ? $meta['field_spec'][$field]['type'] : 'text';
            if(isset($function)) $type = 'number';
            
            switch($type) {
                case 'text':
                    $rtype = 'string';
                    break;
                case 'number':
                    $rtype = 'number';
                    break;
                case 'boolean':
                    $rtype = 'boolean';
                    break;
                case 'date':
                    $rtype = 'date';
                    break;
                case 'datetime':
                case 'timestamp':
                    $rtype = 'datetime';
                    break;
                case 'time':
                    $rtype = 'time';
                    break;
                default:
                    throw new MC_Google_Visualization_Error('Unknown field type "' . $type . '"');
            }

            $field_init[] = "{id:'" . $field_id . "',label:" . $this->jsonEncode($label) . ",type:'" . $rtype . "'}";
        }

        return "{cols: [" . implode(',', $field_init) . "],rows: [";
    }

    /**
     * Given an associative array of key => value pairs and the results of generateMetadata, return the visualization results fragment for the particular row
     * @param array $row the row values as an array
     * @param array $meta the metadata for the query (use generateMetadata())
     * @return string the string fragment to include in the results back to the javascript client
     */
    public function getRowValues($row, $meta) {
        $vals = array();
        foreach($meta['select'] as $field) {
            if(is_array($field)) {
                $function = $field[0];
                if(isset($field[2])) {
                    $key = implode(',', $field[2]) . ' ' . $function . '-' . $field[1];
                } else {
                    $key = $function . '-' .  $field[1];
                }
                $field = $field[1];
            } else {
                $function = null;
                $key = $field;
            }

            $field_meta = $meta['field_spec'][$field];
            if( isset($field_meta['callback']) && $field_meta['callback'] ) {
                if(isset($field_meta['extra'])) {
                    $params = array($row, $field_meta['fields']);
                    $params = array_merge($params, $field_meta['extra']);
                    $val = call_user_func_array($field_meta['callback'], $params);
                } else {
                    $val = call_user_func($field_meta['callback'], $row, $field_meta['fields']);
                }
            } else {
                $val = $row[$key];
            }

            $type = (isset($function)) ? 'number' : $field_meta['type'];

            if(isset($meta['formats'][$field])) {
                $format = $meta['formats'][$field];
            } elseif(isset($this->default_format[$type])) {
                $format = $this->default_format[$type];
            }
            
            switch($type) {
                case '':
                case null:
                case 'text':
                    $val = $this->jsonEncode((string) $val);
                    $formatted = null;
                    break;
                case 'number':
                    $val = (float) $val;
                    if(preg_match('/^num:(\d+)(.*)$/i', $format, $matches)) {
                        $digits = (int) $matches[1];
                        $extras = $matches[2];
                        if($extras) {
                            $formatted = number_format($val, $digits, $extras[0], $extras[1]);
                        } else {
                            $formatted = number_format($val, $digits);
                        }
                    } elseif($format == 'dollars') {
                        $formatted = '$' . number_format($val, 2);
                    } elseif($format == 'percent') {
                        $formatted = number_format($val * 100, 1) . '%';
                    } else {
                        $formatted = sprintf($format, $val);
                    }
                    $val = $this->jsonEncode($val);
                    break;
                case 'boolean':
                    $val = (bool) $val;
                    list($format_false, $format_true) = explode(':', $format, 2);
                    $formatted = ($val) ? $format_true : $format_false;
                    $val = $this->jsonEncode((bool) $val);
                    break;
                case 'date':
                    if(!is_numeric($val) || strlen($val) != 6) {
                        $time = strtotime($val);
                        list($year, $month, $day) = explode('-', date('Y-m-d', $time));
                        $formatted = date($format, $time);
                    } else {
                        $year = substr($val, 0, 4);
                        $week = substr($val, -2);
                        $time = strtotime($year . '0104 +' . ($week) . ' weeks');
                        $monday = strtotime('-' . (date('w', $time) - 1) . ' days', $time);
                        list($year, $month, $day) = explode('-', date('Y-m-d', $monday));
                        $formatted = date($format, $monday);
                    }
                    $val = 'new Date(' . (int) $year . ',' . ($month - 1) . ',' . (int) $day . ')';
                    break;
                case 'datetime':
                case 'timestamp':
                    $time = strtotime($val);
                    list($year, $month, $day, $hour, $minute, $second) = explode('-', date('Y-m-d-H-i-s', $time));
                    $val = 'new Date(' . (int) $year . ',' . ($month - 1) . ',' . (int) $day . ',' . (int) $hour . ',' . (int) $minute . ',' . (int) $second . ')';
                    $formatted = date($format, $time);
                    break;
                case 'time':
                    $time = strtotime($val);
                    list($hour, $minute, $second) = explode('-', date('H-i-s', $time));
                    $val = '[' . (int) $hour . ',' . (int) $minute . ',' . (int) $second . ',0]';
                    $formatted = date($format, $time);
                    break;
                default:
                    throw new MC_Google_Visualization_Error('Unknown field type "' . $type . '"');
            }

            if(!isset($meta['options']['no_values'])) {
                $cell = '{v:' . $val;
                if(!isset($meta['options']['no_format'])) {
                    if($formatted !== null) $cell .= ',f:' . $this->jsonEncode($formatted);
                }
            } else {
                $cell = '{f:' . $this->jsonEncode($formatted);
            }
            
            $vals[] =  $cell . '}';
        }
        return '{c:[' . implode(',', $vals) . ']}';
    }

    public function getSuccessClose() {
        return ']}});';
    }

    /**
     * Encode a value to JSON, using either Zend or the built-in php functions, depending on what's available
     * @param mixed $php the PHP variable to serialize
     * @return string the JSON representation of the variable
     */
    public function jsonEncode($php) {
        if(class_exists('Zend_Json')) {
            return Zend_Json::encode($php);
        } else {
            return json_encode($php);
        }
    }

    /**
     * A utility method for testing - take a visualization query, and return the SQL that would be generated
     * @param string $query the visualization query to run
     * @return string the SQL that should be sent to the database
     */
    public function getSQL($query) {
        $tokens = $this->parseQuery($query);
        $meta = $this->generateMetadata($tokens);
        return $this->generateSQL($meta);
    }

    /**
     * Use MC_Parser to generate a grammar that matches the query language specified here: http://code.google.com/apis/visualization/documentation/querylanguage.html
     * @return MC_Parser_Def the grammar for the query language
     */
    public function getGrammar() {
        $p = new MC_Parser();
        $ident = $p->oneOf(
            $p->word($p->alphas() . '_', $p->alphanums() . '_'),
            $p->quotedString('`')
        );

        $literal = $p->oneOf(
            $p->number()->name('number'),
            $p->quotedString()->name('string'),
            $p->boolean('lower')->name('boolean'),
            $p->set($p->keyword('date', true), $p->quotedString())->name('date'),
            $p->set($p->keyword('timeofday', true), $p->quotedString())->name('time'),
            $p->set(
                $p->oneOf($p->keyword('datetime', true),
                          $p->keyword('timestamp', true)),
                $p->quotedString()
            )->name('datetime')
        );

        $function = $p->set($p->oneOf($p->literal('min', true), $p->literal('max', true), $p->literal('count', true), $p->literal('avg', true), $p->literal('sum', true))->name('func_name'), $p->literal('(')->suppress(), $ident, $p->literal(')')->suppress())->name('function');

        $select = $p->set($p->keyword('select', true), $p->oneOf($p->keyword('*'), $p->delimitedList($p->oneOf($function, $ident))))->name('select');
        $from = $p->set($p->keyword('from', true), $ident)->name('from');

        $comparison = $p->oneOf($p->literal('<'), $p->literal('<='), $p->literal('>'), $p->literal('>='), $p->literal('='), $p->literal('!='), $p->literal('<>'))->name('operator');

        $expr = $p->recursive();
        $value = $p->oneOf($literal, $ident->name('where_field'));
        $cond = $p->oneOf(
            $p->set($value, $comparison, $value),
            $p->set($value, $p->set($p->keyword('is', true), $p->literal('null', true))->name('isnull')),
            $p->set($value, $p->set($p->keyword('is', true), $p->keyword('not', true), $p->literal('null', true))->name('notnull')),
            $p->set($p->literal('(')->name('sep'), $expr, $p->literal(')')->name('sep'))
        );

        $andor = $p->oneOf($p->keyword('and', true), $p->keyword('or', true))->name('andor_sep');

        $expr->replace($p->set($cond, $p->zeroOrMore($p->set($andor, $expr))));

        $where = $p->set($p->keyword('where', true), $expr)->name('where');

        $groupby = $p->set($p->keyword('group', true), $p->keyword('by', true), $p->delimitedList($ident))->name('groupby');
        $pivot = $p->set($p->keyword('pivot', true), $p->delimitedList($ident))->name('pivot');

        $orderby_clause = $p->set($ident, $p->optional($p->oneOf($p->literal('asc', true), $p->literal('desc', true))));
        $orderby = $p->set($p->keyword('order', true), $p->keyword('by', true), $p->delimitedList($orderby_clause))->name('orderby');
        $limit = $p->set($p->keyword('limit', true), $p->word($p->nums()))->name('limit');
        $offset = $p->set($p->keyword('offset', true), $p->word($p->nums()))->name('offset');
        $label = $p->set($p->keyword('label', true), $p->delimitedList($p->set($ident, $p->quotedString())))->name('label');
        $format = $p->set($p->keyword('format', true), $p->delimitedList($p->set($ident, $p->quotedString())))->name('format');
        $options = $p->set($p->keyword('options', true), $p->delimitedList($p->word($p->alphas() . '_')))->name('options');

        $query = $p->set($p->optional($select), $p->optional($from), $p->optional($where), $p->optional($groupby), $p->optional($pivot), $p->optional($orderby), $p->optional($limit), $p->optional($offset), $p->optional($label), $p->optional($format), $p->optional($options));

        return $query;
    }
}
