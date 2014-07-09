/**
 * Scavix Web Development Framework
 *
 * Copyright (c) 2007-2012 PamConsult GmbH
 * Copyright (c) since 2013 Scavix Software Ltd. & Co. KG
 * 
 * This library is free software; you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General
 * Public License as published by the Free Software Foundation;
 * either version 3 of the License, or (at your option) any
 * later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library. If not, see <http://www.gnu.org/licenses/>
 *
 * @author PamConsult GmbH http://www.pamconsult.com <info@pamconsult.com>
 * @copyright 2007-2012 PamConsult GmbH
 * @author Scavix Software Ltd. & Co. KG http://www.scavix.com <info@scavix.com>
 * @copyright since 2012 Scavix Software Ltd. & Co. KG
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 */
using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Drawing;
using System.Data;
using System.Linq;
using System.Text;
using System.Windows.Forms;
using System.Threading;
using System.IO;
using Newtonsoft.Json.Linq;
using System.IO.Compression;
using System.Configuration;
using Newtonsoft.Json;
using System.Runtime.Serialization;
using System.Runtime;
using Microsoft.VisualBasic.Devices;
using System.Text.RegularExpressions;

namespace WdfTracer
{
    public delegate void CloseRequestDelegate(LogView sender, string reason);
    public delegate void ChangeDetected(LogView sender);

    public partial class LogView : UserControl
    {
        #region nested classes

        [JsonObject]
        class Entry
        {
            [JsonProperty("dt")]
            public DateTime Created { get; set; }
            [JsonProperty("sev")]
            public string Severity { get; set; }
            [JsonProperty("cat")]
            public List<string> Categories { get; set; }
            [JsonProperty("msg")]
            public string Message { get; set; }
            [JsonProperty("trace")]
            public List<StackEntry> StackTrace { get; set; }

            public string ShortMessage { get; set; }

            internal void PostCreation()
            {
                if (Message != null)
                {
                    string[] tmp = Message.Split(messageSplitter);
                    if (tmp.Length > 1)
                    {
                        ShortMessage = tmp[0] + " [...]";
                        Message = string.Join(Environment.NewLine, tmp);
                    }
                    else
                        ShortMessage = Message;
                }
                else
                {
                    ShortMessage = "";
                    Message = "";
                }

                if (StackTrace != null && StackTrace.Count > 0)
                    foreach (StackEntry st in StackTrace)
                        st.PostCreation();
            }
        }
        [JsonObject]
        class StackEntry
        {
            [JsonProperty("class")]
            public string Class { get; set; }
            [JsonProperty("type")]
            public string Type { get; set; }
            [JsonProperty("function")]
            public string Function { get; set; }
            [JsonProperty("line")]
            public int Line { get; set; }
            [JsonProperty("location")]
            public string Location { get; set; }
            [JsonProperty("args")]
            public List<object> Args { get; set; }

            public string Details { get; set; }

            internal void PostCreation()
            {
                Details = "";
                if (Args == null)
                    return;
                int i = 0;
                foreach (object a in Args)
                    Details += "Argument " + i++ + " = " + (a == null ? "NULL" : a.ToString()) + Environment.NewLine;
            }
        }

        #endregion

        #region fields

        private delegate void VoidDelegate();
        private delegate void UpdateListDelegate(bool fullRepaint);
        private delegate void AddEntryDelegate(Entry o);

        public event CloseRequestDelegate OnCloseRequested;

        private string filename;
        private GZipStream zipstream;
        private Stream stream;
        private StreamReader reader;
        private Thread parser;
        private List<Entry> buffer;
        private List<Entry> visibleItems;

        private System.Threading.Timer watcher;
        private int refreshInterval = 1000;

        private List<string> visibleSeverities;

        private System.Threading.Timer filterTimer;
        private string currentFilter = "";
        private long filterDelay = 1000;

        private string currentSearchText = "";

        private List<string> knownCategories;
        private List<string> hiddenCategories;
        private bool categoryFilterActive = false;

        private List<string> knownSeverities;

        private bool autoScrollNeeded = false;

        internal static char[] messageSplitter = new char[] { '\n' };
        internal static char[] categorySplitter = new char[] { ',' };

        public ProgressOverlay Progress;

        private MemoryFailPoint memChecker;
        private int memoryPerLine = 0;

        private DateTime minDateTime = DateTime.MinValue;
        private DateTime maxDateTime = DateTime.MinValue;
        private DateTime[] currentDateTimeFilter = null;

        //private Regex textlineReg1 = new Regex(@"\[(\d{4}-\d{2}-\d{2}\x20\d{2}:\d{2}:\d{2}\.\d*)\]\x20\[([^\]]*)\]\x20\(([^\)]*)\)\t(.*)");
        //private Regex textlineReg2 = new Regex(@"^\[([^\]]+)\]\s(.*)");
        private Regex textlineReg = null;
        private List<Regex> textLinePatterns = new List<Regex>();

        #endregion

        public event ChangeDetected OnChangeDetected;
        private bool AsJson = true;
        private bool AsText { get { return !AsJson; } }

        public LogView()
        {
            InitializeComponent();
            Dock = DockStyle.Fill;
            lvEntries.VirtualMode = true;

            panLower.Height = Program.LogViewPanLowerHeight;
            lvStackEntries.Width = Program.LogViewStackEntriesWidth;

            chDateTime.Width = Program.EntriesDateColumnWidth;
            chSeverity.Width = Program.EntriesSevColumnWidth;
            chCategories.Width = Program.EntriesCatColumnWidth;
            chMessage.Width = Program.EntriesMsgColumnWidth;

            chCall.Width = Program.StackCallColumnWidth;
            chLocation.Width = Program.StackLocColumnWidth;

            Progress = new ProgressOverlay();
            Progress.OnCancelled += new CancelledDelegate(Progress_OnCancelled);

            foreach (string p in File.ReadAllLines("textline.patterns"))
            {
                string l = p.Trim();
                if (l == "")
                    continue;
                Regex r = new Regex(l,RegexOptions.IgnoreCase);
                textLinePatterns.Add(r);
            }
            textlineReg = textLinePatterns[0];
        }

        #region common stuff

        void Progress_OnCancelled(ProgressOverlay sender)
        {
            Terminate();
            RequestClose("");
        }

        public void Terminate()
        {
            if (parser != null && parser.IsAlive)
                parser.Abort();
            if (watcher != null)
                watcher.Dispose();
            if (reader != null)
                reader.Close();

            Progress.OnCancelled -= new CancelledDelegate(Progress_OnCancelled);

            lvEntries.VirtualListSize = 0;
            lvEntries.VirtualMode = false;
            buffer.Clear();
            visibleItems.Clear();
            knownCategories.Clear();
            hiddenCategories.Clear();
        }

        private void PrepareReader()
        {
            if (filename.EndsWith(".gz"))
            {
                FileStream tmp = new FileStream(filename, FileMode.Open, FileAccess.Read, FileShare.ReadWrite);
                zipstream = new GZipStream(tmp, CompressionMode.Decompress);
                stream = new MemoryStream();
                zipstream.CopyTo(stream);
                zipstream.Close();
                tmp.Close();
                stream.Seek(0, SeekOrigin.Begin);
            }
            else
                stream = new FileStream(filename, FileMode.Open, FileAccess.Read, FileShare.ReadWrite);
            reader = new StreamReader(stream);
        }

        internal bool SetFile(string p, bool is_json)
        {
            try
            {
                AsJson = is_json;

                filename = p;
                buffer = new List<Entry>();
                visibleItems = new List<Entry>();
                knownCategories = new List<string>();
                knownCategories.Add("*EMPTY*");
                hiddenCategories = new List<string>();
                knownSeverities = new List<string>();

                PrepareReader();
                parser = new Thread(new ThreadStart(Parse));

                cbSeverityFilter_Changed(null, null);
                parser.Start();
                return true;
            }
            catch (Exception ex) { Program.Log(ex); }
            return false;
        }

        public void TermiateTasks()
        {
            if (parser != null && parser.IsAlive)
                parser.Abort();

            if (watcher != null)
                watcher.Dispose();
        }

        private void RequestClose(string reason)
        {
            if (OnCloseRequested != null)
                OnCloseRequested(this, reason);
        }

        private void ReportProgress(long min, long max, long position)
        {
            Progress.SetProgress(min, max, position);
        }

        internal bool ViewsFile(string filename)
        {
            return this.filename == filename;
        }

        #endregion

        #region JSON/Text loading and parsing

        //private void FreeResources()
        //{
        //    if (memoryPerLine > 0 && buffer.Count > 250)
        //    {
        //        try
        //        {
        //            memChecker = new MemoryFailPoint(memoryPerLine);
        //        }
        //        catch (InsufficientMemoryException)
        //        {
        //            int amount = Math.Min(500, (int)Math.Ceiling((double)buffer.Count / 4));
        //            for (int i = 0; i < amount; i++)
        //            {
        //                if (visibleItems.Contains(buffer[0]))
        //                    visibleItems.Remove(buffer[0]);
        //                buffer.RemoveAt(i);
        //            }
        //        }
        //    }
        //}

        private void Parse()
        {
            int err_count = 0;
            int succ_count = 0;
            string line = "";
            Entry test = null;
            while (!reader.EndOfStream)
            {
                try
                {
                    //if( succ_count%10 == 0 )
                    //    FreeResources();
                    //long mem1 = GC.GetTotalMemory(true);
                    line = reader.ReadLine();
                    
                    if( AsJson )
                        test = JsonConvert.DeserializeObject<Entry>(line);
                    else
                    {
                        Entry parsed = ParseTextLine(line, test, test==null);
                        if( parsed == null )
                            continue;
                        test = parsed;
                    }
                    AddToBuffer(test);
                    succ_count++;
                    //long mem2 = mem1 - GC.GetTotalMemory(false);
                    //if (mem2 > memoryPerLine)
                    //    memoryPerLine = (int)Math.Ceiling((double)mem2 / (1024 * 1024));
                }
                catch (Exception ex)
                {
                    err_count++;
                    Program.LogParseError(line, ex);
                }
                ReportProgress(0, stream.Length, stream.Position);

                if (succ_count == 0 && err_count > 10)
                {
                    RequestClose("Invalid tracefile.");
                    return;
                }
            }
            if (buffer.Count > 0)
            {
                minDateTime = buffer[0].Created;
                maxDateTime = buffer[buffer.Count - 1].Created;
            }

            parser = null;
            InvalidateKnownCategories();
            InvalidateLvEntries();
            if( stream is FileStream )
                watcher = new System.Threading.Timer(new TimerCallback(Watch), null, refreshInterval, Timeout.Infinite);
        }

        private void Watch(object state)
        {
            // try to access the reader to test if the file is still open
            try { bool dummy = reader.EndOfStream; }
            catch { PrepareReader(); } // if not try to reopen

            try
            {
                FileInfo fi = new FileInfo(filename);
                if (fi.Length < stream.Position)
                {
                    try
                    {
                        stream.Close();
                        stream = new FileStream(filename, FileMode.Open, FileAccess.Read, FileShare.ReadWrite);
                        reader = new StreamReader(stream);
                    }
                    catch (Exception ex)
                    {
                        Program.Log(ex);
                    }
                }

                bool updateNeeded = false;
                string line = "";
                Entry test = null;
                while (!reader.EndOfStream)
                {
                    try
                    {
                        //FreeResources();
                        line = reader.ReadLine();
                        if (AsJson)
                            test = JsonConvert.DeserializeObject<Entry>(line);
                        else
                        {
                            Entry parsed = ParseTextLine(line, test, false);
                            if (parsed == null)
                                continue;
                            test = parsed;
                        }

                        AddToBuffer(test);
                        if (!updateNeeded)
                        {
                            updateNeeded = true;
                            if (minDateTime == DateTime.MinValue)
                                minDateTime = test.Created;
                        }
                    }
                    catch (Exception ex)
                    {
                        Program.LogParseError(line,ex);
                    }
                }
                if (updateNeeded)
                {
                    maxDateTime = buffer[buffer.Count - 1].Created;
                    InvalidateKnownCategories();
                    InvalidateLvEntries();
                    if (OnChangeDetected != null)
                        OnChangeDetected(this);
                }
            }
            catch (Exception ex)
            {
                Program.Log(ex);
            }
            watcher = new System.Threading.Timer(new TimerCallback(Watch), null, refreshInterval, Timeout.Infinite);
        }

        private Entry ParseTextLine(string line, Entry lastEntry, bool enable_format_detection)
        {
            Match m = textlineReg.Match(line);

            if (!m.Success && enable_format_detection)
            {
                int i = textLinePatterns.IndexOf(textlineReg) + 1;
                if (i > 0 && i < textLinePatterns.Count)
                {
                    textlineReg = textLinePatterns[i];
                    m = textlineReg.Match(line);
                }
            }

            Entry test = null;
            if (m.Success)
            {
                test = new Entry();
                string[] gn = textlineReg.GetGroupNames();
                if (gn.Contains("y") && gn.Contains("m") && gn.Contains("d") && gn.Contains("h") && gn.Contains("i") && gn.Contains("s"))
                {
                    test.Created = new DateTime(
                        int.Parse(m.Groups["y"].Value), txtToMonth(m.Groups["m"].Value), int.Parse(m.Groups["d"].Value),
                        int.Parse(m.Groups["h"].Value), int.Parse(m.Groups["i"].Value), int.Parse(m.Groups["s"].Value)
                        );
                }
                else if (gn.Contains("date"))
                    test.Created = DateTime.Parse(m.Groups["date"].Value);

                test.Severity = gn.Contains("sev")?m.Groups["sev"].Value:"";
                test.Categories = gn.Contains("sev")?new List<string>(m.Groups["cat"].Value.Split(categorySplitter)):new List<string>();
                test.Message = gn.Contains("msg")?m.Groups["msg"].Value:"";
                return test;
            }

            if (lastEntry == null)
            {
                lastEntry.Message += "\n" + line;
                lastEntry.PostCreation();
            }
            return null;
        }

        private int txtToMonth(string month)
        {
            switch (month.ToLower())
            {
                case "jan": return 1;
                case "feb": return 2;
                case "mar":
                case "märz":
                            return 3;
                case "apr": return 4;
                case "may":
                case "mai":    
                            return 5;
                case "jun":
                case "june":
                case "juni":
                            return 6;
                case "jul":
                case "july":
                case "juli":
                            return 7;
                case "aug": return 8;
                case "sep":
                case "sept":
                            return 9;
                case "oct":
                case "okt":
                    return 10;
                case "nov": return 11;
                case "dec":
                case "dez":
                           return 12;
            }
            return int.Parse(month);
        }

        private bool IsVisibleItem(Entry o)
        {
            if ( o.Severity != "" && !visibleSeverities.Contains(o.Severity))
                return false;
            if (currentFilter != "" && !o.Message.ToLower().Contains(currentFilter))
                return false;

            if (currentDateTimeFilter != null)
            {
                if (o.Created < currentDateTimeFilter[0] || o.Created > currentDateTimeFilter[1])
                    return false;
            }

            if (categoryFilterActive)
            {
                if (o.Categories.Count > 0)
                {
                    foreach (string c in o.Categories)
                        if (hiddenCategories.Contains(c))
                            return false;
                }
                else if (hiddenCategories.Contains("*EMPTY"))
                    return false;
            }
            return true;
        }

        private void AddToBuffer(Entry o)
        {
            if (InvokeRequired)
            {
                Invoke(new AddEntryDelegate(AddToBuffer), new object[] { o });
                return;
            }
            o.PostCreation();
            buffer.Add(o);
            foreach (string c in o.Categories)
            {
                if (!knownCategories.Contains(c))
                    knownCategories.Add(c);
            }

            if (!knownSeverities.Contains(o.Severity))
                knownSeverities.Add(o.Severity);

            if (parser != null || IsVisibleItem(o))
            {
                visibleItems.Add(o);
                autoScrollNeeded = true;
            }
        }

        #endregion

        #region lvEntries handler methods

        private void lvEntries_RetrieveVirtualItem(object sender, RetrieveVirtualItemEventArgs e)
        {
            if (e.ItemIndex < 0 || e.ItemIndex >= visibleItems.Count)
                return;
            Entry o = visibleItems[e.ItemIndex];

            ListViewItem item = new ListViewItem(DateTime.Parse(o.Created.ToString()).ToString());
            item.SubItems.Add(string.Join(",", o.Categories));
            item.SubItems.Add(o.Severity);
            item.SubItems.Add(o.ShortMessage.Replace("\t", " "));
            item.Tag = o;

            if (lvEntries.SelectedIndices.Count > 0 && e.ItemIndex == lvEntries.SelectedIndices[0])
                item.ImageIndex = imageList1.Images.IndexOfKey("imgPlay");

            if (currentSearchText != "")
            {
                foreach (ListViewItem.ListViewSubItem sub in item.SubItems)
                {
                    if (sub.Text.ToLower().Contains(currentSearchText))
                    {
                        item.BackColor = Color.Aqua;
                        break;
                    }
                }
            }

            e.Item = item;
        }

        private void lvEntries_SelectedIndexChanged(object sender, EventArgs e)
        {
            bool clear = (lvEntries.SelectedIndices.Count == 0) ||
                (lvEntries.SelectedIndices[0] < 0) ||
                (lvEntries.SelectedIndices[0] >= visibleItems.Count);

            if (clear)
            {
                tbMessage.Text = "";
                lvStackEntries.BeginUpdate();
                lvStackEntries.Items.Clear();
                lvStackEntries.EndUpdate();
                cbAutoScroll.Checked = true;
                tbStackDetails.Text = "";
                return;
            }

            cbAutoScroll.Checked = false;
            Entry o = visibleItems[lvEntries.SelectedIndices[0]];
            tbMessage.Text = o.Message;

            lvStackEntries.BeginUpdate();
            lvStackEntries.Items.Clear();

            if (o.StackTrace != null)
            {
                tpStacktrace.Text = "Stacktrace";
                foreach (StackEntry st in o.StackTrace)
                {
                    string func = (st.Class != null) ? st.Class + st.Type + st.Function : st.Function;
                    ListViewItem lvi = lvStackEntries.Items.Add(func);
                    lvi.SubItems.Add(st.Location);
                    lvi.Tag = st;
                    if (lvStackEntries.Items.Count == 1)
                        lvi.Selected = true;
                }
            }
            else
                tpStacktrace.Text = "<no stacktrace found>";

            lvStackEntries.EndUpdate();
        }

        private void lvEntries_ColumnWidthChanged(object sender, ColumnWidthChangedEventArgs e)
        {
            ColumnHeader ch = (sender as ListView).Columns[e.ColumnIndex];
            if (ch == chDateTime)
                Program.EntriesDateColumnWidth = ch.Width;
            else if (ch == chSeverity)
                Program.EntriesSevColumnWidth = ch.Width;
            else if (ch == chCategories)
                Program.EntriesCatColumnWidth = ch.Width;
            else if (ch == chMessage)
                Program.EntriesMsgColumnWidth = ch.Width;
            else if (ch == chCall)
                Program.StackCallColumnWidth = ch.Width;
            else if (ch == chLocation)
                Program.StackLocColumnWidth = ch.Width;
        }

        private void lvEntries_DoubleClick(object sender, EventArgs e)
        {
            if (lvEntries.SelectedIndices.Count == 0)
                return;

            Entry o = visibleItems[lvEntries.SelectedIndices[0]];
            if (o.StackTrace == null || o.StackTrace.Count == 0)
                return;

            //if (!o.ShortMessage.Contains(o.StackTrace[0].Location))
            //    return;

            lvStackEntries.Items[0].Selected = true;
            lvStackEntries_DoubleClick(null, null);
        }

        private void lvEntries_ColumnClick(object sender, ColumnClickEventArgs e)
        {
            ColumnHeader ch = lvEntries.Columns[e.Column];

            List<DateTime> availableHours = new List<DateTime>();
            foreach (Entry entry in buffer)
            {
                DateTime h = entry.Created.Date.AddHours(entry.Created.Hour);
                if (!availableHours.Contains(h))
                    availableHours.Add(h);
            }

            cmDateFilter.Items.Clear();
            DateTime current = minDateTime;
            while (current.Date <= maxDateTime.Date)
            {
                ToolStripMenuItem item = new ToolStripMenuItem(current.ToString("d"));
                item.Click += new EventHandler(cmDateTimeItem_Click);
                item.Tag = new DateTime[] { current.Date, current.Date.AddDays(1) };

                DateTime hours = current.Date;
                while (hours.Hour < current.Hour)
                    hours = hours.AddHours(1);
                while (hours < maxDateTime && hours.Date == current.Date)
                {
                    if (availableHours.Contains(hours))
                    {
                        ToolStripMenuItem h_item = new ToolStripMenuItem(hours.ToString("T"));
                        h_item.Click += new EventHandler(cmDateTimeItem_Click);
                        h_item.Tag = new DateTime[] { hours, hours.AddHours(1) };
                        item.DropDownItems.Add(h_item);
                    }
                    hours = hours.AddHours(1);
                }

                cmDateFilter.Items.Add(item);
                current = current.Date.AddDays(1);
            }

            ToolStripItem clr = cmDateFilter.Items.Add("Clear filter");
            clr.Click += new EventHandler(cmDateTimeItem_Click);

            cmDateFilter.Show(Cursor.Position);
        }

        void cmDateTimeItem_Click(object sender, EventArgs e)
        {
            ToolStripItem item = sender as ToolStripItem;
            currentDateTimeFilter = item.Tag == null ? null : (DateTime[])item.Tag;
            cmDateFilter.Hide();
            if (currentDateTimeFilter == null)
                chDateTime.Text = "DateTime";
            else
                chDateTime.Text = "DateTime [" + currentDateTimeFilter[0].ToString("G") + "]";
            InvalidateVisibleItems();
        }

        #endregion

        #region lvStackEntries handler methods

        private void lvStackEntries_SelectedIndexChanged(object sender, EventArgs e)
        {
            if (lvStackEntries.SelectedItems.Count == 0)
            {
                tbStackDetails.Text = "";
                return;
            }

            StackEntry o = (StackEntry)lvStackEntries.SelectedItems[0].Tag;
            tbStackDetails.Text = o.Details;
            
        }

        private void lvStackEntries_Resize(object sender, EventArgs e)
        {
            Program.LogViewStackEntriesWidth = lvStackEntries.Width;
        }

        private void lvStackEntries_DoubleClick(object sender, EventArgs e)
        {
            if (lvStackEntries.SelectedItems.Count == 0)
                return;

            if (!Program.AnyViewerReady())
            {
                MessageBox.Show("No supported viewer programs found yet (search still in progress). Please try again later.");
                return;
            }

            string machine = Program.GetMachineName(filename);
            if (machine == "")
            {
                MessageBox.Show("Cannot substitute a local path to a network machine.");
                return;
            }

            StackEntry se = (lvStackEntries.SelectedItems[0].Tag as StackEntry);
            string[] parts = se.Location.Split(new char[] { ':' });
            string path = string.Join(":", parts, 0, parts.Length - 1);
            bool seems_windows = path.Length > 1 && path[1] == ':';
            string local_file = Program.SubstitutePath(machine, path);

            if (local_file == "")
            {
                MessageBox.Show("Please browse to your local copy of the file" + Environment.NewLine +
                    "'" + path + "'" + Environment.NewLine +
                    "and 'open' it." + Environment.NewLine +
                    "This step is needed for path substitution.");
                if (dlgBrowseToLocalSource.ShowDialog() != DialogResult.OK)
                    return;

                if (Path.GetFileName(dlgBrowseToLocalSource.FileName) != Path.GetFileName(path))
                {
                    MessageBox.Show("You'll have to select the correct file.");
                    return;
                }

                string[] local = dlgBrowseToLocalSource.FileName.Split(new char[] { Path.DirectorySeparatorChar }).Reverse().ToArray();
                string[] remote = path.Split(new char[] { '/', '\\' }).Reverse().ToArray();
                for (int i = 0; i < Math.Min(local.Length, remote.Length); i++)
                {
                    if (local[i].ToLower() != remote[i].ToLower())
                    {
                        List<string> localsub = new List<string>();
                        for (int j = i; j < local.Length; j++)
                            localsub.Add(local[j]);
                        List<string> remotesub = new List<string>();
                        for (int j = i; j < remote.Length; j++)
                            remotesub.Add(remote[j]);

                        localsub.Reverse();
                        remotesub.Reverse();

                        if( seems_windows )
                            Program.AddPathSubstitution(machine,
                                (string.Join("\\", remotesub)).Replace("\\\\", "\\"),
                                string.Join(Path.DirectorySeparatorChar + "", localsub));
                        else
                            Program.AddPathSubstitution(machine,
                                ("/" + string.Join("/", remotesub)).Replace("//", "/"),
                                string.Join(Path.DirectorySeparatorChar + "", localsub));
                        break;
                    }
                }
                local_file = Program.SubstitutePath(machine, parts[0]);
            }
            Program.OpenInEditor(local_file, se.Line);
        }

        #endregion

        #region other UIs handler methods

        private void cbAutoScroll_CheckedChanged(object sender, EventArgs e)
        {
            if( cbAutoScroll.Checked )
                lvEntries.SelectedIndices.Clear();
        }

        private void cbSeverityFilter_Changed(object sender, EventArgs e)
        {
            visibleSeverities = new List<string>();
            if (cbTrace.Checked) visibleSeverities.Add("TRACE");
            if (cbDebug.Checked) visibleSeverities.Add("DEBUG");
            if (cbInfo.Checked) visibleSeverities.Add("INFO");
            if (cbWarn.Checked) visibleSeverities.Add("WARN");
            if (cbError.Checked) visibleSeverities.Add("ERROR");
            if (cbFatal.Checked) visibleSeverities.Add("FATAL");

            InvalidateVisibleItems();
        }

        private void listView3_ItemChecked(object sender, ItemCheckedEventArgs e)
        {
            if (lvCategories.Enabled)
            {
                hiddenCategories.Clear();
                foreach (ListViewItem c in lvCategories.Items)
                    if (c.Checked)
                        hiddenCategories.Add(c.Text);

                categoryFilterActive = hiddenCategories.Count != lvCategories.Items.Count;
                InvalidateVisibleItems();
            }
        }

        private void btnClearList_Click(object sender, EventArgs e)
        {
            buffer.Clear();
            visibleItems.Clear();
            knownCategories.Clear();
            knownCategories.Add("*EMPTY*");
            hiddenCategories.Clear();
            categoryFilterActive = false;
            lvCategories.Items.Clear();
            lvEntries.VirtualListSize = 0;
            tbMessage.Text = "";
            lvStackEntries.Items.Clear();
            tbStackDetails.Text = "";
            minDateTime = DateTime.MinValue;
            maxDateTime = DateTime.MinValue;
        }

        private void panLower_Resize(object sender, EventArgs e)
        {
            Program.LogViewPanLowerHeight = panLower.Height;
        }

        #endregion

        #region filtering and searching

        private void tbFilter_KeyPress(object sender, KeyPressEventArgs e)
        {
            if (filterTimer != null)
                filterTimer.Dispose();
            filterTimer = new System.Threading.Timer(new TimerCallback(PerformFiltering), null, filterDelay, Timeout.Infinite);
        }

        public void PerformFiltering(object state)
        {
            currentFilter = tbFilter.Text.ToLower();
            InvalidateVisibleItems();
        }

        private void tbSearch_KeyUp(object sender, KeyEventArgs e)
        {
            currentSearchText = tbSearch.Text.ToLower();
            lvEntries.Invalidate();
        }

        #endregion

        #region invalidate UI stuff

        private void InvalidateKnownCategories()
        {
            if (InvokeRequired)
            {
                Invoke(new VoidDelegate(InvalidateKnownCategories));
                return;
            }
            if (knownCategories != null)
            {
                lvCategories.BeginUpdate();
                lvCategories.Enabled = false;
                foreach (string c in knownCategories)
                {
                    if (lvCategories.Items.ContainsKey(c))
                        continue;
                    ListViewItem ci = lvCategories.Items.Add(c, c, 0);
                    ci.Checked = false;
                }
                lvCategories.EndUpdate();
                lvCategories.Enabled = true;
            }
        }

        private void InvalidateLvEntries()
        {
            if (InvokeRequired)
            {
                Invoke(new VoidDelegate(InvalidateLvEntries));
                return;
            }
            lvEntries.VirtualListSize = visibleItems.Count;
            if (lvEntries.VirtualListSize > 0 && cbAutoScroll.Checked && autoScrollNeeded)
            {
                autoScrollNeeded = false;
                lvEntries.EnsureVisible(lvEntries.VirtualListSize - 1);
            }
        }

        private void InvalidateVisibleItems()
        {
            if (InvokeRequired)
            {
                Invoke(new VoidDelegate(InvalidateVisibleItems));
                return;
            }
            lvEntries.VirtualListSize = 0;
            visibleItems.Clear();
            foreach (Entry o in buffer)
            {
                if (IsVisibleItem(o))
                    visibleItems.Add(o);
            }
            lvEntries.VirtualListSize = visibleItems.Count;
            lvEntries.Invalidate();
        }

        #endregion
    }
}
