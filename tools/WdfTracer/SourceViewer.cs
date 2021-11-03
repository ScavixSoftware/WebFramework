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
using System.Text;
using System.Drawing;
using Microsoft.Win32;
using System.IO;
using System.Windows.Forms;
using System.Diagnostics;
using System.Runtime.InteropServices;
using System.Threading;
using System.Drawing.IconLib;

namespace WdfTracer
{
    internal class SourceViewer
    {
        private string argPat = "{file}";

        public string Alias { get; set; }
        public string Name { get; set; }
        public string ArgumentPattern { get { return argPat; } set { argPat = value; } }
        public string ExeSearchName { get; set; }
        public string Executable { get; set; }
        public int IdleBeforeLineMark { get; set; }
        public bool SeemsNotInstalled = false;
        public int Nummer { get; set; }
        public Image Image
        {
            get
            {
                if (IsReady())
                {
                    try
                    {
                        string iconfile = Path.Combine(Application.UserAppDataPath, Alias + ".ico");
                        if (!File.Exists(iconfile))
                        {
                            FileStream icon = File.Create(iconfile);
                            MultiIcon mIcon = new MultiIcon();
                            mIcon.Load(Executable);
                            foreach (SingleIcon si in mIcon)
                            {
                                si.Icon.Save(icon);
                            }
                            icon.Flush();
                            icon.Close();
                        }
                        Icon i = new Icon(iconfile);
                        return i.ToBitmap();
                    } catch (Exception e)
                    {
                        return null;
                    }
                }
                return null;
            }
        }
        internal int[] Bubble(int[] a)
        {
            int swap = 0;
            for (int runs = 0; runs < a.Length; runs++)
            {
                for (int sort = 0; sort < a.Length - 1; sort++)
                {
                    if (a[sort] > a[sort + 1])
                    {
                        swap = a[sort + 1];
                        a[sort + 1] = a[sort];
                        a[sort] = swap;
                    }
                }
            }
            return a;
        }
        internal void DeleteIcon()
        {
            string iconfile = Path.Combine(Application.UserAppDataPath, Alias + ".ico");
            File.Delete(Path.GetFullPath(iconfile));
        }
        internal void Save(RegistryKey settings)
        {
            RegistryKey key = settings.CreateSubKey(Alias);
            key.SetValue("Name", (Name == null) ? "": Name, RegistryValueKind.String);
            key.SetValue("ExeSearchName", (ExeSearchName == null) ? "": ExeSearchName, RegistryValueKind.String);
            key.SetValue("Executable", (Executable == null) ? "" : Executable, RegistryValueKind.String);
            key.SetValue("ArgumentPattern", (ArgumentPattern == null) ? "" : ArgumentPattern, RegistryValueKind.String);
            key.SetValue("Nummer", Nummer, RegistryValueKind.DWord);
            if (IdleBeforeLineMark == 0)
                IdleBeforeLineMark = 500;
            key.SetValue("IdleBeforeLineMark", IdleBeforeLineMark, RegistryValueKind.DWord);
        }

        internal void Delete(RegistryKey settings)
        {
            if (settings.GetSubKeyNames().Contains(Alias))
                settings.DeleteSubKey(Alias);
        }

        internal bool IsReady()
        {
            return Executable != null && File.Exists(Executable);
        }

        [DllImport("user32.dll")]
        private static extern bool SetForegroundWindow(IntPtr hWnd);

        internal bool Run(string file, int line)
        {
            if (!IsReady())
            {
                Program.Log(Name + " executable not yet found.");
                return false;
            }
            string args = ArgumentPattern.Replace("{file}", file).Replace("{line}", line.ToString());
            Process p = Process.Start(Executable,args);
            if (IdleBeforeLineMark > 0)
            {
                SetForegroundWindow(p.MainWindowHandle);
                Thread.Sleep(IdleBeforeLineMark);
                SendKeys.Send("+{DOWN}");
            }
            return true;
        }

        public override bool Equals(object obj)
        {
            if (obj is SourceViewer && (obj as SourceViewer).Alias == Alias)
                return true;
            return false;
        }
    }
}
