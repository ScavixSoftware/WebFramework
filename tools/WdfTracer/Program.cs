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
using System.Linq;
using System.Windows.Forms;
using System.Threading;
using System.IO;
using Microsoft.Win32;
using System.Runtime.InteropServices;
using Microsoft.VisualBasic.ApplicationServices;
using System.Diagnostics;
using System.Security.Cryptography;
using System.Drawing;

namespace WdfTracer
{
    static class Program
    {
        #region nested classes

        public class SingleInstanceController : WindowsFormsApplicationBase
        {
            private string[] Arguments;

            public SingleInstanceController(string[] args)
            {
                Arguments = args;
                IsSingleInstance = true;
                StartupNextInstance += this_StartupNextInstance;
#if !DEBUG
                SetAssociation(".trace", "Wdf_Tracer_File", Application.ExecutablePath, "WdfTracer file");
                SetAssociation(".trace.gz", "Wdf_Tracer_File_GZ", Application.ExecutablePath, "Archived WdfTracer file");
#endif
                searchViewer = new Thread(new ThreadStart(SearchViewer));
                searchViewer.Start();
            }

            void this_StartupNextInstance(object sender, StartupNextInstanceEventArgs e)
            {
                (MainForm as Form1).SetCommandlineArgs(e.CommandLine.ToArray<string>());
            }

            protected override void OnCreateMainForm()
            {
                MainForm = new Form1();
                (MainForm as Form1).SetCommandlineArgs(Arguments);
            }

            public void InformMainformAboutException(Exception ex)
            {
                try
                {
                    (MainForm as Form1).ProgramExceptionOccured(ex);
                } 
                catch(Exception ex2) 
                {
                    MessageBox.Show(ex2.Message, "Exception occured"); 
                }
            }

            public void InformMainformAboutViewerChange()
            {
                (MainForm as Form1).InvalidateSelectedViewer();
            }

            private void SetAssociation(string Extension, string KeyName, string OpenWith, string FileDescription)
            {
                RegistryKey BaseKey;
                RegistryKey OpenMethod;
                RegistryKey Shell;

                RegistryKey root = Registry.CurrentUser.CreateSubKey("Software");
                root = root.CreateSubKey("Classes");

                BaseKey = root.CreateSubKey(Extension);
                BaseKey.SetValue("", KeyName);

                OpenMethod = root.CreateSubKey(KeyName);
                OpenMethod.SetValue("", FileDescription);
                OpenMethod.CreateSubKey("DefaultIcon").SetValue("", "\"" + OpenWith + "\",0");
                Shell = OpenMethod.CreateSubKey("Shell");
                Shell.CreateSubKey("edit").CreateSubKey("command").SetValue("", "\"" + OpenWith + "\"" + " \"%1\"");
                Shell.CreateSubKey("open").CreateSubKey("command").SetValue("", "\"" + OpenWith + "\"" + " \"%1\"");
                BaseKey.Close();
                OpenMethod.Close();
                Shell.Close();
            }
        }

        #endregion

        #region fields

        private static Thread searchViewer;
        private static SingleInstanceController controller;
        internal static RegistryKey settings;
        internal static RegistryKey ViewerSettings;

        private static string logfile_name;
        private static FileStream debug_stream;
        private static StreamWriter debug;

        #endregion

        #region imports

        [DllImport("shell32.dll", EntryPoint = "ShellExecute")]
        public static extern long ShellExecute(int hwnd, string cmd, string file, string param1, string param2, int swmode);

        [DllImport("shell32.dll", CharSet = CharSet.Auto, SetLastError = true)]
        private static extern void SHChangeNotify(uint wEventId, uint uFlags, IntPtr dwItem1, IntPtr dwItem2);

        #endregion

        /// <summary>
        /// The main entry point for the application.
        /// </summary>
        [STAThread]
        static void Main(string[] args)
        {
            Application.EnableVisualStyles();
            Application.SetCompatibleTextRenderingDefault(false);

            string path = Application.UserAppDataRegistry.Name.Substring(0, Application.UserAppDataRegistry.Name.LastIndexOf("\\"));
            path = path.Substring(path.IndexOf('\\') + 1);
            RegistryKey root = Registry.CurrentUser;
            settings = root.CreateSubKey(path);
            ViewerSettings = settings.CreateSubKey("Viewer");

            Application.ThreadException += new ThreadExceptionEventHandler(Application_ThreadException);
            Application.SetUnhandledExceptionMode(UnhandledExceptionMode.CatchException);

            Log("Startup");
            controller = new SingleInstanceController(args);
            controller.Run(args);
        }

        static void Application_ThreadException(object sender, ThreadExceptionEventArgs e)
        {
            Log(e.Exception);
        }

        /// <summary>
        /// Will be called from MainForm in closing handler
        /// </summary>
        internal static void TerminateThreads()
        {
            if (searchViewer != null && searchViewer.IsAlive)
                searchViewer.Abort();
        }

        #region logging

        public static void Log(string text)
        {
            string prefix = DateTime.Now.ToString("G") + "\t";
            if (debug == null)
            {
                bool deleted = false;
                logfile_name = Path.Combine(Path.GetTempPath(), "wdf_tracer.log");
                if (File.Exists(logfile_name))
                {
                    FileInfo fi = new FileInfo(logfile_name);
                    if (fi.Length > Program.MaxErrorlogSize)
                    {
                        try
                        {
                            File.Delete(logfile_name);
                            deleted = true;
                        }
                        catch { }
                    }
                }
                debug_stream = new FileStream(logfile_name, FileMode.Append, FileAccess.Write, FileShare.ReadWrite);
                debug = new StreamWriter(debug_stream);
                if (deleted)
                    debug.WriteLine(prefix + "Logfile truncated");
            }
            debug.WriteLine(prefix + text);
            debug.Flush();
        }

        public static void Log(Exception ex)
        {
            Log("Exception: " + ex.Message + Environment.NewLine + ex.StackTrace);
            controller.InformMainformAboutException(ex);
        }

        public static void OpenLogfile()
        {
            ShellExecute(0, "open", logfile_name, "", "", 5);
        }

        internal static void LogParseError(string line, Exception ex)
        {
            Log("Parse Error in line: " + line + Environment.NewLine + "Message: " + ex.Message + Environment.NewLine + ex.StackTrace);
        }

        #endregion

        #region viewer handling

        private static void SearchViewer()
        {
            
            foreach (SourceViewer s in Viewer)
            {
                if (s.Executable != null && File.Exists(s.Executable))
                    continue;

                try
                {
                    Log("Searching viewer " + s.Name);
                    string[] files = GetFiles(@"C:\", s.ExeSearchName)
                        .OrderBy(item => item, new NaturalStringComparer())
                        .ToArray();

                    if (files.Length < 1)
                    {
                        Log(s.Name + " not found. Not installed? Not on drive 'C:'?");
                        s.SeemsNotInstalled = true;
                        controller.InformMainformAboutViewerChange();
                        continue;
                    }
                    else if (files.Length > 1)
                        Log(s.Name + " found multiple times, taking first." + Environment.NewLine + string.Join(Environment.NewLine, files.ToArray()));

                    s.Executable = files[0];
                    s.Save(ViewerSettings);
                    Log("Found " + s.Name + " executable: '" + s.Executable + "'");
                    if (SelectedViewer == null)
                        SelectedViewer = s;
                    controller.InformMainformAboutViewerChange();
                }
                catch (Exception ex)
                {
                    Log(ex);
                }
            }
        }

        public static IEnumerable<string> GetFiles(string root, string searchPattern)
        {
            Stack<string> pending = new Stack<string>();
            pending.Push(root);
            while (pending.Count != 0)
            {
                var path = pending.Pop();
                string[] next = null;
                try
                {
                    next = Directory.GetFiles(path, searchPattern);
                }
                catch { }
                if (next != null && next.Length != 0)
                    foreach (var file in next) yield return file;
                try
                {
                    next = Directory.GetDirectories(path);
                    foreach (var subdir in next) pending.Push(subdir);
                }
                catch { }
            }
        }

        internal static bool AnyViewerReady()
        {
            foreach (SourceViewer s in Viewer)
                if (s.Executable != null && File.Exists(s.Executable))
                    return true;
            return false;
        }

        internal static bool OpenInEditor(string file, int line)
        {
            if (SelectedViewer == null || !SelectedViewer.IsReady())
                return false;

            return SelectedViewer.Run(file, line);
        }

        #endregion

        #region path substitution

        internal static string GetMachineName(string path)
        {
            string remote_machine = Path.GetPathRoot(path);
            if (!remote_machine.StartsWith(@"\\"))
                return path;
            return remote_machine.Substring(2).Split(new char[] { '\\' })[0];
        }

        internal static string SubstitutePath(string remote_machine, string remote)
        {
            string path = Application.UserAppDataRegistry.Name.Substring(0, Application.UserAppDataRegistry.Name.LastIndexOf("\\"));
            path = path.Substring(path.IndexOf('\\') + 1);
            RegistryKey root = Registry.CurrentUser;
            root = root.CreateSubKey(path);
            root = root.CreateSubKey("PathSubstitutes");
            root = root.CreateSubKey(remote_machine.Replace(@"\", "_"));
            foreach (string n in root.GetValueNames())
            {
                if (remote.StartsWith(n))
                    return remote.Replace(n, root.GetValue(n, "").ToString());
            }
            return "";
        }

        internal static void AddPathSubstitution(string remote_machine, string remote, string local)
        {
            string path = Application.UserAppDataRegistry.Name.Substring(0, Application.UserAppDataRegistry.Name.LastIndexOf("\\"));
            path = path.Substring(path.IndexOf('\\') + 1);
            RegistryKey root = Registry.CurrentUser;
            root = root.CreateSubKey(path);
            root = root.CreateSubKey("PathSubstitutes");
            root = root.CreateSubKey(remote_machine.Replace(@"\", "_"));
            root.SetValue(remote, local, RegistryValueKind.String);
        }

        #endregion

        #region settings

        internal static Point WindowPosition
        {
            get { 
                string[] tmp = settings.GetValue("WindowPosition", "10;10").ToString().Split(new char[]{';'});
                return new Point(int.Parse(tmp[0]), int.Parse(tmp[1]));
            }
            set { settings.SetValue("WindowPosition", value.X + ";"+value.Y, RegistryValueKind.String); }
        }

        internal static Size WindowSize
        {
            get
            {
                string[] tmp = settings.GetValue("WindowSize", "800;500").ToString().Split(new char[] { ';' });
                return new Size(int.Parse(tmp[0]), int.Parse(tmp[1]));
            }
            set { settings.SetValue("WindowSize", value.Width + ";" + value.Height, RegistryValueKind.String); }
        }

        internal static FormWindowState WindowState
        {
            get {
                string tmp = settings.GetValue("WindowState", "Normal").ToString();
                switch( tmp )
                {
                    case "Minimized": return FormWindowState.Minimized;
                    case "Maximized": return FormWindowState.Maximized;
                }
                return FormWindowState.Normal;
            }
            set { settings.SetValue("WindowState", value.ToString(), RegistryValueKind.String); }
        }

        internal static SourceViewer SelectedViewer
        {
            get { 
                string alias = settings.GetValue("SelectedViewer", "netbeans").ToString();
                foreach( SourceViewer s in Viewer )
                    if( alias == s.Alias && s.IsReady() )
                        return s;
                foreach (SourceViewer s in Viewer)
                    if (s.IsReady())
                    {
                        settings.SetValue("SelectedViewer", s.Alias, RegistryValueKind.String);
                        return s;
                    }
                return null;
            }
            set { settings.SetValue("SelectedViewer", value.Alias, RegistryValueKind.String); }
        }

        internal static int LogViewPanLowerHeight
        {
            get { return int.Parse(settings.GetValue("LogViewPanLowerHeight", "250").ToString()); }
            set { settings.SetValue("LogViewPanLowerHeight", value, RegistryValueKind.DWord); }
        }

        internal static int LogViewStackEntriesWidth
        {
            get { return int.Parse(settings.GetValue("LogViewStackEntriesWidth", "450").ToString()); }
            set { settings.SetValue("LogViewStackEntriesWidth", value, RegistryValueKind.DWord); }
        }

        internal static int EntriesDateColumnWidth
        {
            get { return int.Parse(settings.GetValue("EntriesDateColumnWidth", "140").ToString()); }
            set { settings.SetValue("EntriesDateColumnWidth", value, RegistryValueKind.DWord); }
        }

        internal static int EntriesSevColumnWidth
        {
            get { return int.Parse(settings.GetValue("EntriesSevColumnWidth", "100").ToString()); }
            set { settings.SetValue("EntriesSevColumnWidth", value, RegistryValueKind.DWord); }
        }

        internal static int EntriesCatColumnWidth
        {
            get { return int.Parse(settings.GetValue("EntriesCatColumnWidth", "100").ToString()); }
            set { settings.SetValue("EntriesCatColumnWidth", value, RegistryValueKind.DWord); }
        }

        internal static int EntriesMsgColumnWidth
        {
            get { return int.Parse(settings.GetValue("EntriesMsgColumnWidth", "350").ToString()); }
            set { settings.SetValue("EntriesMsgColumnWidth", value, RegistryValueKind.DWord); }
        }

        internal static int StackCallColumnWidth
        {
            get { return int.Parse(settings.GetValue("StackCallColumnWidth", "200").ToString()); }
            set { settings.SetValue("StackCallColumnWidth", value, RegistryValueKind.DWord); }
        }

        internal static int StackLocColumnWidth
        {
            get { return int.Parse(settings.GetValue("StackLocColumnWidth", "200").ToString()); }
            set { settings.SetValue("StackLocColumnWidth", value, RegistryValueKind.DWord); }
        }

        internal static int MaxErrorlogSize
        {
            get { return int.Parse(settings.GetValue("MaxErrorlogSize", "20").ToString()); }
            set { settings.SetValue("MaxErrorlogSize", value, RegistryValueKind.DWord); }
        }

        internal static List<string> RecentFiles
        {
            get
            {
                RegistryKey mru = settings.CreateSubKey("MRU");
                List<string> res = new List<string>();
                foreach (string n in mru.GetValueNames())
                {
                    string fn = mru.GetValue(n, "").ToString();
                    if (File.Exists(fn))
                        res.Add(fn);
                    else
                        Log("Recent file does not exist anymore: " + fn);
                }
                return res;
            }
        }

        internal static void AddRecentFile(string filename)
        {
            List<string> recent = RecentFiles;
            if (recent.Contains(filename))
                recent.Remove(filename);

            recent.Insert(0, filename);
            while (recent.Count > 20)
                recent.RemoveAt(recent.Count - 1);

            RegistryKey mru = settings.CreateSubKey("MRU");
            foreach (string n in mru.GetValueNames())
                mru.DeleteValue(n);

            for (int i = 0; i < recent.Count; i++)
                mru.SetValue(i.ToString("D3"), recent[i], RegistryValueKind.String);
        }

        internal static List<SourceViewer> Viewer
        {
            get
            {
                List<SourceViewer>  viewer = new List<SourceViewer>();
                RegistryKey root = settings.CreateSubKey("Viewer");
                List<string> res = new List<string>();

                if (root.GetSubKeyNames().Length == 0)
                {
                    viewer = new List<SourceViewer>();
                    viewer.Add(new SourceViewer { Alias = "netbeans", Name = "NetBeans", ArgumentPattern = "--nosplash {file}:{line}", ExeSearchName = "netbeans.exe" });
                    viewer.Add(new SourceViewer { Alias = "ultraedit", Name = "UltraEdit", ArgumentPattern = "/foi \"{file}/{line}\"", ExeSearchName = "uedit32.exe" });
                    viewer.Add(new SourceViewer { Alias = "notepadpp", Name = "Notepad++", ArgumentPattern = "-n{line} \"{file}\"", ExeSearchName = "notepad++.exe" });
                    viewer.Add(new SourceViewer { Alias = "notepad", Name = "Notepad", ArgumentPattern = "{file}", ExeSearchName = "notepad.exe"});

                    foreach (SourceViewer s in viewer)
                        s.Save(root);

                    return viewer;
                }

                foreach (string n in root.GetSubKeyNames())
                {
                    RegistryKey key = root.OpenSubKey(n);
                    SourceViewer s = new SourceViewer();
                    s.Alias = n;
                    s.Name = key.GetValue("Name", n).ToString();
                    s.ExeSearchName = key.GetValue("ExeSearchName", "").ToString();
                    s.Executable = key.GetValue("Executable", "").ToString();
                    s.ArgumentPattern = key.GetValue("ArgumentPattern", "").ToString();
                    s.IdleBeforeLineMark = int.Parse(key.GetValue("IdleBeforeLineMark", "500").ToString());
                    s.Nummer = int.Parse(key.GetValue("Nummer", "0").ToString());

                    viewer.Add(s);
                }
                SourceViewer swap;
                for (int runs = 0; runs < viewer.Count; runs++)
                {
                    for (int sort = 0; sort < viewer.Count - 1; sort++)
                    {
                        if (viewer[sort].Nummer > viewer[sort + 1].Nummer)
                        {
                            swap = viewer[sort + 1];
                            viewer[sort + 1] = viewer[sort];
                            viewer[sort] = swap;
                        }
                    }
                }

                return viewer;
            }
        }

        #endregion
    }
}
