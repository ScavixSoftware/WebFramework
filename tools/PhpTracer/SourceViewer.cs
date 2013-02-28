/**
 * PamConsult Web Development Framework
 *
 * Copyright (c) 2007-2012 PamConsult GmbH
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

namespace PhpTracer
{
    internal class SourceViewer
    {
        public string Alias { get; set; }
        public string Name { get; set; }
        public string ArgumentPattern { get; set; }
        public string ExeSearchName { get; set; }
        public string Executable { get; set; }
        public int IdleBeforeLineMark { get; set; }
        public bool SeemsNotInstalled = false;

        public Image Image
        {
            get
            {
                if (IsReady())
                {
                    string iconfile = Path.Combine(Application.UserAppDataPath, Alias + ".ico");
                    if (File.Exists(iconfile))
                    {
                        Icon i = new Icon(iconfile);
                        return i.ToBitmap();
                    }
                    else
                    {
                        using (TKageyu.Utils.IconExtractor ie = new TKageyu.Utils.IconExtractor(Executable))
                        {
                            if (ie.IconCount > 0)
                            {
                                FileStream icon = File.Create(iconfile);
                                Icon i = ie.GetIcon(0);
                                i.Save(icon);
                                icon.Close();
                                return i.ToBitmap();
                            }
                        }
                    }
                }
                return null;
            }
        }

        internal void Save(RegistryKey settings)
        {
            RegistryKey key = settings.CreateSubKey(Alias);
            key.SetValue("Name", Name, RegistryValueKind.String);
            key.SetValue("ExeSearchName", ExeSearchName, RegistryValueKind.String);
            key.SetValue("Executable", (Executable == null) ? "" : Executable, RegistryValueKind.String);
            key.SetValue("ArgumentPattern", ArgumentPattern, RegistryValueKind.String);

            if (IdleBeforeLineMark == 0)
                IdleBeforeLineMark = 500;
            key.SetValue("IdleBeforeLineMark", IdleBeforeLineMark, RegistryValueKind.DWord);
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
