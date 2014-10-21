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
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Windows.Forms;
using Newtonsoft.Json;
using Newtonsoft.Json.Linq;
using System.IO;
using System.Runtime.InteropServices;

namespace WdfTracer
{
    public partial class Form1 : Form
    {
        #region delegates

        private delegate void OpenFileDelegate(string filename);
        private delegate void ExceptionDelegate(Exception ex);
        private delegate void InvalidateSelectedViewerDelegate();

        #endregion

        #region fields

        private int exception_counter = 0;

        #endregion

        public Form1()
        {
            InitializeComponent();
            tcFileViews.TabPages.Clear();
            InvalidateSelectedViewer();
        }

        #region external interface

        internal void SetCommandlineArgs(string[] args)
        {
            for (int i = 0; i < args.Length; i++)
                OpenFile(args[i]);
        }

        internal void ProgramExceptionOccured(Exception ex)
        {
            if (InvokeRequired)
            {
                Invoke(new ExceptionDelegate(ProgramExceptionOccured), new object[] { ex });
                return;
            }
            exception_counter++;
            labExceptions.Image = Properties.Resources.error;
            labExceptions.Text = exception_counter + " Exceptions";
            labExceptions.ToolTipText = "Last exception: " + ex.Message + Environment.NewLine 
                + ex.StackTrace + Environment.NewLine 
                + "Click to open Logfile";
        }

        public void InvalidateSelectedViewer()
        {
            if (InvokeRequired)
            {
                Invoke(new InvalidateSelectedViewerDelegate(InvalidateSelectedViewer));
                return;
            }
            btnSelectedViewer.DropDownItems.Clear();
            btnSelectedViewer.Text = "No viewer found";
            btnSelectedViewer.Image = Properties.Resources.none;

            foreach (SourceViewer s in Program.Viewer)
            {
                ToolStripItem i = btnSelectedViewer.DropDownItems.Add(s.Name);
                i.Tag = s;
                i.Image = s.Image;
                i.Click += new EventHandler(ViewerItemClick);
                i.Enabled = s.IsReady();
                if (!i.Enabled)
                {
                    if( s.SeemsNotInstalled )
                        i.Text += " (not found)";
                    else
                        i.Text += " (still searching)";
                }

                if (s.Equals(Program.SelectedViewer))
                {
                    btnSelectedViewer.Text = "Selected Viewer: " + i.Text;
                    btnSelectedViewer.Image = i.Image;
                }
            }
            btnSelectedViewer.DropDownItems.Add("-");
            ToolStripItem manage = btnSelectedViewer.DropDownItems.Add("Manage");
            manage.Click += manage_Click;
            
        }

        void manage_Click(object sender, EventArgs e)
        {
            Form2 dlg = new Form2();
            if (dlg.ShowDialog() == DialogResult.OK)
            { InvalidateSelectedViewer(); }
        }

        private void ViewerItemClick(object sender, EventArgs e)
        {
            Program.SelectedViewer = (sender as ToolStripItem).Tag as SourceViewer;
            InvalidateSelectedViewer();
        }
        
        #endregion

        #region internal tool methods

        private void OpenFile(string filename)
        {
            Program.Log("Opening file: " + filename);
            string fn = filename.ToLower();
            bool is_json = fn.EndsWith(".trace") || fn.EndsWith(".trace.gz");

            try
            {
                foreach (TabPage t in tcFileViews.TabPages)
                {
                    foreach (Control c in t.Controls)
                        if (c is LogView && (c as LogView).ViewsFile(filename))
                        {
                            tcFileViews.SelectedTab = t;
                            return;
                        }
                }
                LogView lv = new LogView();
                lv.OnCloseRequested += new CloseRequestDelegate(lv_OnCloseRequested);
                lv.OnChangeDetected += new ChangeDetected(lv_OnChangeDetected);
                TabPage page = new TabPage(" " + Path.GetFileName(filename) + " ");
                page.Controls.Add(lv);
                page.Controls.Add(lv.Progress);
                tcFileViews.TabPages.Add(page);

                if (!lv.SetFile(filename, is_json))
                {
                    MessageBox.Show("Unable to open file '" + filename + "'");
                    tcFileViews.TabPages.Remove(page);
                }
                else
                {
                    page.ToolTipText = filename;
                    tcFileViews.SelectedTab = page;
                    Program.AddRecentFile(filename);
                }
            }
            catch(Exception ex)
            {
                Program.Log(ex);
            }
            InvalidateRecentItems();
        }

        private void InvalidateRecentItems()
        {
            btnOpen.DropDownItems.Clear();
            foreach (string fn in Program.RecentFiles)
            {
                ToolStripItem item = btnOpen.DropDownItems.Add(fn);
                item.Tag = fn;
                item.ToolTipText = fn;
                item.DisplayStyle = ToolStripItemDisplayStyle.ImageAndText;
                item.Image = Icon.ToBitmap();
                item.Click += new EventHandler(RecentItemClick);
            }
        }

        private void RecentItemClick(object sender, EventArgs e)
        {
            ToolStripItem item = sender as ToolStripItem;
            OpenFile(item.Tag.ToString());
        }

        #endregion        

        #region LogView handling

        void lv_OnCloseRequested(LogView sender, string reason)
        {
            if (InvokeRequired)
            {
                Invoke(new CloseRequestDelegate(lv_OnCloseRequested), new object[] { sender, reason });
                return;
            }
            if (reason != "")
                MessageBox.Show(reason);

            TabPage page = null;
            foreach (TabPage p in tcFileViews.TabPages)
            {
                foreach (Control c in p.Controls)
                    if (c == sender)
                    {
                        page = p;
                        break;
                    }
                if (page != null)
                    break;
            }
            if (page == null)
                return;
            tcFileViews.SelectedTab.Controls.Clear();
            tcFileViews.TabPages.Remove(page);
            GC.Collect();
        }

        void lv_OnChangeDetected(LogView sender)
        {
            if (InvokeRequired)
            {
                Invoke(new ChangeDetected(lv_OnChangeDetected), new object[] { sender });
                return;
            }

            foreach (TabPage t in tcFileViews.TabPages)
            {
                if (t == tcFileViews.SelectedTab)
                    continue;
                foreach (Control c in t.Controls)
                    if (c == sender)
                    {
                        t.Tag = "changed";
                        tcFileViews.Invalidate();
                        return;
                    }
            }
        }

        private void tcFileViews_Selected(object sender, TabControlEventArgs e)
        {
            if (e.TabPage != null)
            {
                e.TabPage.Tag = null;
                tcFileViews.Invalidate();
            }
        }

        private void tcFileViews_DrawItem(object sender, DrawItemEventArgs e)
        {
            Graphics g = e.Graphics;
            Rectangle rect = e.Bounds;
            TabPage p = tcFileViews.TabPages[e.Index];
            Font f = (p.Tag == null) ? tcFileViews.Font : new Font(tcFileViews.Font, FontStyle.Bold);

            StringFormat format = new StringFormat();
            format.Alignment = StringAlignment.Center;
            format.LineAlignment = StringAlignment.Center;
            format.FormatFlags |= StringFormatFlags.NoWrap;
            format.FormatFlags |= StringFormatFlags.NoClip;

            g.DrawString(p.Text.Trim(), f, Brushes.Black, rect, format);
        }

        private void tabPage1_MouseUp(object sender, MouseEventArgs e)
        {
            if (e.Button == System.Windows.Forms.MouseButtons.Middle && tcFileViews.SelectedTab != null)
            {

                foreach (Control c in tcFileViews.SelectedTab.Controls)
                    if (c is LogView)
                        (c as LogView).Terminate();
                tcFileViews.TabPages.Remove(tcFileViews.SelectedTab);
            }
        }

        #endregion

        #region common window handlers

        private void Form1_Resize(object sender, EventArgs e)
        {
            if (WindowState == FormWindowState.Normal)
                Program.WindowSize = Size;
        }

        private void Form1_LocationChanged(object sender, EventArgs e)
        {
            if (WindowState == FormWindowState.Normal)
                Program.WindowPosition = Location;
        }

        private void Form1_Load(object sender, EventArgs e)
        {
            Location = Program.WindowPosition;
            Size = Program.WindowSize;
            WindowState = Program.WindowState;
            Text += " [V" + Application.ProductVersion + "]";
            InvalidateRecentItems();
        }

        private void Form1_FormClosing(object sender, FormClosingEventArgs e)
        {
            Program.WindowState = WindowState;
            Program.TerminateThreads();
        }

        #endregion

        #region Drag and Drop

        private void Form1_DragEnter(object sender, DragEventArgs e)
        {
            if (e.Data.GetDataPresent(DataFormats.FileDrop))
            {
                Array a = (Array)e.Data.GetData(DataFormats.FileDrop);
                if (a != null)
                {
                    foreach (var entry in a)
                    {
                        string fn = entry.ToString();
                        if (fn.ToLower().EndsWith(".trace") || fn.ToLower().EndsWith(".trace.gz") || fn.ToLower().EndsWith(".log") || fn.ToLower().EndsWith(".log.gz"))
                        {
                            e.Effect = DragDropEffects.Copy;
                            break;
                        }
                    }
                }
            }
            else
                e.Effect = DragDropEffects.None;
        }

        private void Form1_DragDrop(object sender, DragEventArgs e)
        {
            try
            {
                Array a = (Array)e.Data.GetData(DataFormats.FileDrop);

                if (a != null)
                {
                    foreach (var entry in a)
                    {
                        string fn = entry.ToString();
                        if (fn.ToLower().EndsWith(".trace") || fn.ToLower().EndsWith(".trace.gz") || fn.ToLower().EndsWith(".log") || fn.ToLower().EndsWith(".log.gz"))
                        {
                            this.BeginInvoke(new OpenFileDelegate(OpenFile), new Object[] { fn });
                        }
                    }
                    this.Activate();
                }
            }
            catch (Exception ex)
            {
                Program.Log(ex);
            }
        }

        #endregion
        
        #region other controls handler methods

        private void btnOpen_Click(object sender, EventArgs e)
        {
            dlgOpenFile.FileName = "";
            if (Program.RecentFiles.Count > 0)
            {
                dlgOpenFile.FileName = Program.RecentFiles[0];
                dlgOpenFile.InitialDirectory = Path.GetDirectoryName(dlgOpenFile.FileName);
            }

            DialogResult res = dlgOpenFile.ShowDialog();
            if (res != System.Windows.Forms.DialogResult.OK)
                return;

            foreach (string filename in dlgOpenFile.FileNames)
            {
                OpenFile(filename);
            }
        }

        private void btnClose_Click(object sender, EventArgs e)
        {
            if (tcFileViews.TabPages.Count > 0)
            {
                foreach (Control c in tcFileViews.SelectedTab.Controls)
                    if (c is LogView)
                        (c as LogView).Terminate();
                tcFileViews.SelectedTab.Controls.Clear();
                tcFileViews.TabPages.Remove(tcFileViews.SelectedTab);
                GC.Collect();
            }
        }

        private void labExceptions_Click(object sender, EventArgs e)
        {
            Program.OpenLogfile();
            if (exception_counter > 0 )
                labExceptions.Image = Properties.Resources.error_gray;
        }

        #endregion        

        private void btnSelectedViewer_Click(object sender, EventArgs e)
        {

        }
    }
}