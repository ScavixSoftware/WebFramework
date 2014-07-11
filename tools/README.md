WdfTracer
=========
WdfTracer is a tool we use to read and monitor logfiles generated with the WebFramework.
It's main purpose is to read the files produced by the TraceLogger class which contain much more information
than normal logfiles.
Of course WdfTracer can be used to monitor any kind of textfile for changes, but many of it's features will not
be useful then.


You may download the source and adjust for your needs or just [grap the binaries](https://github.com/ScavixSoftware/WebFramework/raw/master/tools/WdfTracer_1.0.0.8.zip).     
Just unpack to a folder of your choice and run the executable.      
You may find some more information over in our [codeproject article](http://www.codeproject.com/Articles/553018/Ultra-Rapid-PHP-Application-Development).

DateTime formats
================
WdfTracer supports different date/time formats. If not by default, you can add your own by appending it to textline.patterns.
Each  line in there is a regular expression and WdfTracer will try to match them one after the other until one matches