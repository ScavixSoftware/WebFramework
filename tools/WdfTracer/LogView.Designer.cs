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
namespace WdfTracer
{
    partial class LogView
    {
        /// <summary> 
        /// Required designer variable.
        /// </summary>
        private System.ComponentModel.IContainer components = null;

        /// <summary> 
        /// Clean up any resources being used.
        /// </summary>
        /// <param name="disposing">true if managed resources should be disposed; otherwise, false.</param>
        protected override void Dispose(bool disposing)
        {
            if (disposing )
            {
                if (components != null) components.Dispose();
                //if (reader != null) reader.Dispose();
                if (watcher != null) watcher.Dispose();
                if (zipstream != null) zipstream.Dispose();
                //if (stream != null) stream.Dispose();
                if (memChecker != null) memChecker.Dispose();
                if (filterTimer != null) filterTimer.Dispose();
            }
            base.Dispose(disposing);
        }

        #region Component Designer generated code

        /// <summary> 
        /// Required method for Designer support - do not modify 
        /// the contents of this method with the code editor.
        /// </summary>
        private void InitializeComponent()
        {
            this.components = new System.ComponentModel.Container();
            System.ComponentModel.ComponentResourceManager resources = new System.ComponentModel.ComponentResourceManager(typeof(LogView));
            this.lvEntries = new System.Windows.Forms.ListView();
            this.chDateTime = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.chCategories = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.chSeverity = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.chMessage = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.imageList1 = new System.Windows.Forms.ImageList(this.components);
            this.splitter1 = new System.Windows.Forms.Splitter();
            this.panLower = new System.Windows.Forms.Panel();
            this.tcEntryDetails = new System.Windows.Forms.TabControl();
            this.tpFullMessage = new System.Windows.Forms.TabPage();
            this.tbMessage = new System.Windows.Forms.TextBox();
            this.tpStacktrace = new System.Windows.Forms.TabPage();
            this.panel3 = new System.Windows.Forms.Panel();
            this.label1 = new System.Windows.Forms.Label();
            this.tbStackDetails = new System.Windows.Forms.TextBox();
            this.splitter2 = new System.Windows.Forms.Splitter();
            this.lvStackEntries = new System.Windows.Forms.ListView();
            this.chCall = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.chLocation = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.panel2 = new System.Windows.Forms.Panel();
            this.cbAutoScroll = new System.Windows.Forms.CheckBox();
            this.btnClearList = new System.Windows.Forms.Button();
            this.lvCategories = new System.Windows.Forms.ListView();
            this.cbCategory = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.label2 = new System.Windows.Forms.Label();
            this.tbSearch = new System.Windows.Forms.TextBox();
            this.label4 = new System.Windows.Forms.Label();
            this.tbFilter = new System.Windows.Forms.TextBox();
            this.label3 = new System.Windows.Forms.Label();
            this.cbFatal = new System.Windows.Forms.CheckBox();
            this.cbError = new System.Windows.Forms.CheckBox();
            this.cbWarn = new System.Windows.Forms.CheckBox();
            this.cbInfo = new System.Windows.Forms.CheckBox();
            this.cbDebug = new System.Windows.Forms.CheckBox();
            this.cbTrace = new System.Windows.Forms.CheckBox();
            this.dlgBrowseToLocalSource = new System.Windows.Forms.OpenFileDialog();
            this.cmDateFilter = new System.Windows.Forms.ContextMenuStrip(this.components);
            this.asdToolStripMenuItem = new System.Windows.Forms.ToolStripMenuItem();
            this.qweToolStripMenuItem = new System.Windows.Forms.ToolStripMenuItem();
            this.panLower.SuspendLayout();
            this.tcEntryDetails.SuspendLayout();
            this.tpFullMessage.SuspendLayout();
            this.tpStacktrace.SuspendLayout();
            this.panel3.SuspendLayout();
            this.panel2.SuspendLayout();
            this.cmDateFilter.SuspendLayout();
            this.SuspendLayout();
            // 
            // lvEntries
            // 
            this.lvEntries.Columns.AddRange(new System.Windows.Forms.ColumnHeader[] {
            this.chDateTime,
            this.chCategories,
            this.chSeverity,
            this.chMessage});
            this.lvEntries.Dock = System.Windows.Forms.DockStyle.Fill;
            this.lvEntries.FullRowSelect = true;
            this.lvEntries.HideSelection = false;
            this.lvEntries.Location = new System.Drawing.Point(0, 0);
            this.lvEntries.MultiSelect = false;
            this.lvEntries.Name = "lvEntries";
            this.lvEntries.Size = new System.Drawing.Size(616, 343);
            this.lvEntries.SmallImageList = this.imageList1;
            this.lvEntries.TabIndex = 0;
            this.lvEntries.UseCompatibleStateImageBehavior = false;
            this.lvEntries.View = System.Windows.Forms.View.Details;
            this.lvEntries.ColumnClick += new System.Windows.Forms.ColumnClickEventHandler(this.lvEntries_ColumnClick);
            this.lvEntries.ColumnWidthChanged += new System.Windows.Forms.ColumnWidthChangedEventHandler(this.lvEntries_ColumnWidthChanged);
            this.lvEntries.RetrieveVirtualItem += new System.Windows.Forms.RetrieveVirtualItemEventHandler(this.lvEntries_RetrieveVirtualItem);
            this.lvEntries.SelectedIndexChanged += new System.EventHandler(this.lvEntries_SelectedIndexChanged);
            this.lvEntries.DoubleClick += new System.EventHandler(this.lvEntries_DoubleClick);
            // 
            // chDateTime
            // 
            this.chDateTime.Text = "DateTime";
            this.chDateTime.Width = 120;
            // 
            // chCategories
            // 
            this.chCategories.DisplayIndex = 2;
            this.chCategories.Text = "Categories";
            this.chCategories.Width = 100;
            // 
            // chSeverity
            // 
            this.chSeverity.DisplayIndex = 1;
            this.chSeverity.Text = "Severity";
            this.chSeverity.Width = 87;
            // 
            // chMessage
            // 
            this.chMessage.Text = "Message";
            this.chMessage.Width = 332;
            // 
            // imageList1
            // 
            this.imageList1.ImageStream = ((System.Windows.Forms.ImageListStreamer)(resources.GetObject("imageList1.ImageStream")));
            this.imageList1.TransparentColor = System.Drawing.Color.Transparent;
            this.imageList1.Images.SetKeyName(0, "imgPlay");
            // 
            // splitter1
            // 
            this.splitter1.Dock = System.Windows.Forms.DockStyle.Bottom;
            this.splitter1.Location = new System.Drawing.Point(0, 343);
            this.splitter1.Name = "splitter1";
            this.splitter1.Size = new System.Drawing.Size(763, 3);
            this.splitter1.TabIndex = 1;
            this.splitter1.TabStop = false;
            // 
            // panLower
            // 
            this.panLower.Controls.Add(this.tcEntryDetails);
            this.panLower.Dock = System.Windows.Forms.DockStyle.Bottom;
            this.panLower.Location = new System.Drawing.Point(0, 346);
            this.panLower.Name = "panLower";
            this.panLower.Size = new System.Drawing.Size(763, 263);
            this.panLower.TabIndex = 2;
            this.panLower.Resize += new System.EventHandler(this.panLower_Resize);
            // 
            // tcEntryDetails
            // 
            this.tcEntryDetails.Controls.Add(this.tpFullMessage);
            this.tcEntryDetails.Controls.Add(this.tpStacktrace);
            this.tcEntryDetails.Dock = System.Windows.Forms.DockStyle.Fill;
            this.tcEntryDetails.Location = new System.Drawing.Point(0, 0);
            this.tcEntryDetails.Name = "tcEntryDetails";
            this.tcEntryDetails.SelectedIndex = 0;
            this.tcEntryDetails.Size = new System.Drawing.Size(763, 263);
            this.tcEntryDetails.TabIndex = 4;
            // 
            // tpFullMessage
            // 
            this.tpFullMessage.Controls.Add(this.tbMessage);
            this.tpFullMessage.Location = new System.Drawing.Point(4, 22);
            this.tpFullMessage.Name = "tpFullMessage";
            this.tpFullMessage.Padding = new System.Windows.Forms.Padding(3);
            this.tpFullMessage.Size = new System.Drawing.Size(755, 237);
            this.tpFullMessage.TabIndex = 0;
            this.tpFullMessage.Text = "Full message";
            this.tpFullMessage.UseVisualStyleBackColor = true;
            // 
            // tbMessage
            // 
            this.tbMessage.Dock = System.Windows.Forms.DockStyle.Fill;
            this.tbMessage.Font = new System.Drawing.Font("Courier New", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.tbMessage.Location = new System.Drawing.Point(3, 3);
            this.tbMessage.Multiline = true;
            this.tbMessage.Name = "tbMessage";
            this.tbMessage.ScrollBars = System.Windows.Forms.ScrollBars.Both;
            this.tbMessage.Size = new System.Drawing.Size(749, 231);
            this.tbMessage.TabIndex = 1;
            this.tbMessage.WordWrap = false;
            // 
            // tpStacktrace
            // 
            this.tpStacktrace.Controls.Add(this.panel3);
            this.tpStacktrace.Controls.Add(this.splitter2);
            this.tpStacktrace.Controls.Add(this.lvStackEntries);
            this.tpStacktrace.Location = new System.Drawing.Point(4, 22);
            this.tpStacktrace.Name = "tpStacktrace";
            this.tpStacktrace.Padding = new System.Windows.Forms.Padding(3);
            this.tpStacktrace.Size = new System.Drawing.Size(755, 237);
            this.tpStacktrace.TabIndex = 1;
            this.tpStacktrace.Text = "Stacktrace";
            this.tpStacktrace.UseVisualStyleBackColor = true;
            // 
            // panel3
            // 
            this.panel3.Controls.Add(this.label1);
            this.panel3.Controls.Add(this.tbStackDetails);
            this.panel3.Dock = System.Windows.Forms.DockStyle.Fill;
            this.panel3.Location = new System.Drawing.Point(454, 3);
            this.panel3.Name = "panel3";
            this.panel3.Size = new System.Drawing.Size(298, 231);
            this.panel3.TabIndex = 7;
            // 
            // label1
            // 
            this.label1.AutoSize = true;
            this.label1.Location = new System.Drawing.Point(3, 7);
            this.label1.Name = "label1";
            this.label1.Size = new System.Drawing.Size(42, 13);
            this.label1.TabIndex = 4;
            this.label1.Text = "Details:";
            // 
            // tbStackDetails
            // 
            this.tbStackDetails.Anchor = ((System.Windows.Forms.AnchorStyles)((((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Bottom) 
            | System.Windows.Forms.AnchorStyles.Left) 
            | System.Windows.Forms.AnchorStyles.Right)));
            this.tbStackDetails.Font = new System.Drawing.Font("Courier New", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.tbStackDetails.Location = new System.Drawing.Point(6, 22);
            this.tbStackDetails.Multiline = true;
            this.tbStackDetails.Name = "tbStackDetails";
            this.tbStackDetails.ScrollBars = System.Windows.Forms.ScrollBars.Both;
            this.tbStackDetails.Size = new System.Drawing.Size(292, 209);
            this.tbStackDetails.TabIndex = 5;
            this.tbStackDetails.WordWrap = false;
            // 
            // splitter2
            // 
            this.splitter2.Location = new System.Drawing.Point(451, 3);
            this.splitter2.Name = "splitter2";
            this.splitter2.Size = new System.Drawing.Size(3, 231);
            this.splitter2.TabIndex = 6;
            this.splitter2.TabStop = false;
            // 
            // lvStackEntries
            // 
            this.lvStackEntries.Columns.AddRange(new System.Windows.Forms.ColumnHeader[] {
            this.chCall,
            this.chLocation});
            this.lvStackEntries.Dock = System.Windows.Forms.DockStyle.Left;
            this.lvStackEntries.FullRowSelect = true;
            this.lvStackEntries.HideSelection = false;
            this.lvStackEntries.Location = new System.Drawing.Point(3, 3);
            this.lvStackEntries.MultiSelect = false;
            this.lvStackEntries.Name = "lvStackEntries";
            this.lvStackEntries.Size = new System.Drawing.Size(448, 231);
            this.lvStackEntries.TabIndex = 3;
            this.lvStackEntries.UseCompatibleStateImageBehavior = false;
            this.lvStackEntries.View = System.Windows.Forms.View.Details;
            this.lvStackEntries.ColumnWidthChanged += new System.Windows.Forms.ColumnWidthChangedEventHandler(this.lvEntries_ColumnWidthChanged);
            this.lvStackEntries.SelectedIndexChanged += new System.EventHandler(this.lvStackEntries_SelectedIndexChanged);
            this.lvStackEntries.DoubleClick += new System.EventHandler(this.lvStackEntries_DoubleClick);
            this.lvStackEntries.Resize += new System.EventHandler(this.lvStackEntries_Resize);
            // 
            // chCall
            // 
            this.chCall.Text = "Call";
            this.chCall.Width = 193;
            // 
            // chLocation
            // 
            this.chLocation.Text = "Location";
            this.chLocation.Width = 206;
            // 
            // panel2
            // 
            this.panel2.Controls.Add(this.cbAutoScroll);
            this.panel2.Controls.Add(this.btnClearList);
            this.panel2.Controls.Add(this.lvCategories);
            this.panel2.Controls.Add(this.label2);
            this.panel2.Controls.Add(this.tbSearch);
            this.panel2.Controls.Add(this.label4);
            this.panel2.Controls.Add(this.tbFilter);
            this.panel2.Controls.Add(this.label3);
            this.panel2.Controls.Add(this.cbFatal);
            this.panel2.Controls.Add(this.cbError);
            this.panel2.Controls.Add(this.cbWarn);
            this.panel2.Controls.Add(this.cbInfo);
            this.panel2.Controls.Add(this.cbDebug);
            this.panel2.Controls.Add(this.cbTrace);
            this.panel2.Dock = System.Windows.Forms.DockStyle.Right;
            this.panel2.Location = new System.Drawing.Point(616, 0);
            this.panel2.MinimumSize = new System.Drawing.Size(147, 343);
            this.panel2.Name = "panel2";
            this.panel2.Size = new System.Drawing.Size(147, 343);
            this.panel2.TabIndex = 3;
            // 
            // cbAutoScroll
            // 
            this.cbAutoScroll.AutoSize = true;
            this.cbAutoScroll.Checked = true;
            this.cbAutoScroll.CheckState = System.Windows.Forms.CheckState.Checked;
            this.cbAutoScroll.Location = new System.Drawing.Point(6, 32);
            this.cbAutoScroll.Name = "cbAutoScroll";
            this.cbAutoScroll.Size = new System.Drawing.Size(77, 17);
            this.cbAutoScroll.TabIndex = 15;
            this.cbAutoScroll.Text = "Auto-Scroll";
            this.cbAutoScroll.UseVisualStyleBackColor = true;
            this.cbAutoScroll.CheckedChanged += new System.EventHandler(this.cbAutoScroll_CheckedChanged);
            // 
            // btnClearList
            // 
            this.btnClearList.Location = new System.Drawing.Point(6, 3);
            this.btnClearList.Name = "btnClearList";
            this.btnClearList.Size = new System.Drawing.Size(134, 23);
            this.btnClearList.TabIndex = 13;
            this.btnClearList.Text = "Clear List";
            this.btnClearList.UseVisualStyleBackColor = true;
            this.btnClearList.Click += new System.EventHandler(this.btnClearList_Click);
            // 
            // lvCategories
            // 
            this.lvCategories.Anchor = ((System.Windows.Forms.AnchorStyles)((((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Bottom) 
            | System.Windows.Forms.AnchorStyles.Left) 
            | System.Windows.Forms.AnchorStyles.Right)));
            this.lvCategories.CheckBoxes = true;
            this.lvCategories.Columns.AddRange(new System.Windows.Forms.ColumnHeader[] {
            this.cbCategory});
            this.lvCategories.FullRowSelect = true;
            this.lvCategories.HeaderStyle = System.Windows.Forms.ColumnHeaderStyle.None;
            this.lvCategories.Location = new System.Drawing.Point(6, 213);
            this.lvCategories.MultiSelect = false;
            this.lvCategories.Name = "lvCategories";
            this.lvCategories.Size = new System.Drawing.Size(135, 130);
            this.lvCategories.TabIndex = 12;
            this.lvCategories.UseCompatibleStateImageBehavior = false;
            this.lvCategories.View = System.Windows.Forms.View.Details;
            this.lvCategories.ItemChecked += new System.Windows.Forms.ItemCheckedEventHandler(this.listView3_ItemChecked);
            // 
            // cbCategory
            // 
            this.cbCategory.Text = "Category";
            this.cbCategory.Width = 130;
            // 
            // label2
            // 
            this.label2.AutoSize = true;
            this.label2.Location = new System.Drawing.Point(6, 197);
            this.label2.Name = "label2";
            this.label2.Size = new System.Drawing.Size(82, 13);
            this.label2.TabIndex = 11;
            this.label2.Text = "Hide Categories";
            // 
            // tbSearch
            // 
            this.tbSearch.Location = new System.Drawing.Point(6, 174);
            this.tbSearch.Name = "tbSearch";
            this.tbSearch.Size = new System.Drawing.Size(135, 20);
            this.tbSearch.TabIndex = 9;
            this.tbSearch.KeyUp += new System.Windows.Forms.KeyEventHandler(this.tbSearch_KeyUp);
            // 
            // label4
            // 
            this.label4.AutoSize = true;
            this.label4.Location = new System.Drawing.Point(6, 158);
            this.label4.Name = "label4";
            this.label4.Size = new System.Drawing.Size(41, 13);
            this.label4.TabIndex = 8;
            this.label4.Text = "Search";
            // 
            // tbFilter
            // 
            this.tbFilter.Location = new System.Drawing.Point(6, 137);
            this.tbFilter.Name = "tbFilter";
            this.tbFilter.Size = new System.Drawing.Size(135, 20);
            this.tbFilter.TabIndex = 7;
            this.tbFilter.KeyPress += new System.Windows.Forms.KeyPressEventHandler(this.tbFilter_KeyPress);
            // 
            // label3
            // 
            this.label3.AutoSize = true;
            this.label3.Location = new System.Drawing.Point(6, 121);
            this.label3.Name = "label3";
            this.label3.Size = new System.Drawing.Size(29, 13);
            this.label3.TabIndex = 6;
            this.label3.Text = "Filter";
            // 
            // cbFatal
            // 
            this.cbFatal.AutoSize = true;
            this.cbFatal.Checked = true;
            this.cbFatal.CheckState = System.Windows.Forms.CheckState.Checked;
            this.cbFatal.Location = new System.Drawing.Point(83, 101);
            this.cbFatal.Name = "cbFatal";
            this.cbFatal.Size = new System.Drawing.Size(49, 17);
            this.cbFatal.TabIndex = 5;
            this.cbFatal.Text = "Fatal";
            this.cbFatal.UseVisualStyleBackColor = true;
            this.cbFatal.CheckedChanged += new System.EventHandler(this.cbSeverityFilter_Changed);
            // 
            // cbError
            // 
            this.cbError.AutoSize = true;
            this.cbError.Checked = true;
            this.cbError.CheckState = System.Windows.Forms.CheckState.Checked;
            this.cbError.Location = new System.Drawing.Point(6, 101);
            this.cbError.Name = "cbError";
            this.cbError.Size = new System.Drawing.Size(48, 17);
            this.cbError.TabIndex = 4;
            this.cbError.Text = "Error";
            this.cbError.UseVisualStyleBackColor = true;
            this.cbError.CheckedChanged += new System.EventHandler(this.cbSeverityFilter_Changed);
            // 
            // cbWarn
            // 
            this.cbWarn.AutoSize = true;
            this.cbWarn.Checked = true;
            this.cbWarn.CheckState = System.Windows.Forms.CheckState.Checked;
            this.cbWarn.Location = new System.Drawing.Point(83, 78);
            this.cbWarn.Name = "cbWarn";
            this.cbWarn.Size = new System.Drawing.Size(52, 17);
            this.cbWarn.TabIndex = 3;
            this.cbWarn.Text = "Warn";
            this.cbWarn.UseVisualStyleBackColor = true;
            this.cbWarn.CheckedChanged += new System.EventHandler(this.cbSeverityFilter_Changed);
            // 
            // cbInfo
            // 
            this.cbInfo.AutoSize = true;
            this.cbInfo.Checked = true;
            this.cbInfo.CheckState = System.Windows.Forms.CheckState.Checked;
            this.cbInfo.Location = new System.Drawing.Point(6, 78);
            this.cbInfo.Name = "cbInfo";
            this.cbInfo.Size = new System.Drawing.Size(44, 17);
            this.cbInfo.TabIndex = 2;
            this.cbInfo.Text = "Info";
            this.cbInfo.UseVisualStyleBackColor = true;
            this.cbInfo.CheckedChanged += new System.EventHandler(this.cbSeverityFilter_Changed);
            // 
            // cbDebug
            // 
            this.cbDebug.AutoSize = true;
            this.cbDebug.Checked = true;
            this.cbDebug.CheckState = System.Windows.Forms.CheckState.Checked;
            this.cbDebug.Location = new System.Drawing.Point(83, 55);
            this.cbDebug.Name = "cbDebug";
            this.cbDebug.Size = new System.Drawing.Size(58, 17);
            this.cbDebug.TabIndex = 1;
            this.cbDebug.Text = "Debug";
            this.cbDebug.UseVisualStyleBackColor = true;
            this.cbDebug.CheckedChanged += new System.EventHandler(this.cbSeverityFilter_Changed);
            // 
            // cbTrace
            // 
            this.cbTrace.AutoSize = true;
            this.cbTrace.Checked = true;
            this.cbTrace.CheckState = System.Windows.Forms.CheckState.Checked;
            this.cbTrace.Location = new System.Drawing.Point(6, 55);
            this.cbTrace.Name = "cbTrace";
            this.cbTrace.Size = new System.Drawing.Size(54, 17);
            this.cbTrace.TabIndex = 0;
            this.cbTrace.Text = "Trace";
            this.cbTrace.UseVisualStyleBackColor = true;
            this.cbTrace.CheckedChanged += new System.EventHandler(this.cbSeverityFilter_Changed);
            // 
            // dlgBrowseToLocalSource
            // 
            this.dlgBrowseToLocalSource.FileName = "openFileDialog1";
            // 
            // cmDateFilter
            // 
            this.cmDateFilter.Items.AddRange(new System.Windows.Forms.ToolStripItem[] {
            this.asdToolStripMenuItem});
            this.cmDateFilter.Name = "cmDateFilter";
            this.cmDateFilter.Size = new System.Drawing.Size(93, 26);
            // 
            // asdToolStripMenuItem
            // 
            this.asdToolStripMenuItem.DropDownItems.AddRange(new System.Windows.Forms.ToolStripItem[] {
            this.qweToolStripMenuItem});
            this.asdToolStripMenuItem.Name = "asdToolStripMenuItem";
            this.asdToolStripMenuItem.Size = new System.Drawing.Size(92, 22);
            this.asdToolStripMenuItem.Text = "asd";
            // 
            // qweToolStripMenuItem
            // 
            this.qweToolStripMenuItem.Name = "qweToolStripMenuItem";
            this.qweToolStripMenuItem.Size = new System.Drawing.Size(96, 22);
            this.qweToolStripMenuItem.Text = "qwe";
            // 
            // LogView
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 13F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.Controls.Add(this.lvEntries);
            this.Controls.Add(this.panel2);
            this.Controls.Add(this.splitter1);
            this.Controls.Add(this.panLower);
            this.Name = "LogView";
            this.Size = new System.Drawing.Size(763, 609);
            this.panLower.ResumeLayout(false);
            this.tcEntryDetails.ResumeLayout(false);
            this.tpFullMessage.ResumeLayout(false);
            this.tpFullMessage.PerformLayout();
            this.tpStacktrace.ResumeLayout(false);
            this.panel3.ResumeLayout(false);
            this.panel3.PerformLayout();
            this.panel2.ResumeLayout(false);
            this.panel2.PerformLayout();
            this.cmDateFilter.ResumeLayout(false);
            this.ResumeLayout(false);

        }

        #endregion

        private System.Windows.Forms.ListView lvEntries;
        private System.Windows.Forms.ColumnHeader chDateTime;
        private System.Windows.Forms.ColumnHeader chCategories;
        private System.Windows.Forms.ColumnHeader chSeverity;
        private System.Windows.Forms.ColumnHeader chMessage;
        private System.Windows.Forms.Splitter splitter1;
        private System.Windows.Forms.Panel panLower;
        private System.Windows.Forms.TextBox tbMessage;
        private System.Windows.Forms.ListView lvStackEntries;
        private System.Windows.Forms.ColumnHeader chCall;
        private System.Windows.Forms.ColumnHeader chLocation;
        private System.Windows.Forms.Panel panel2;
        private System.Windows.Forms.TextBox tbSearch;
        private System.Windows.Forms.Label label4;
        private System.Windows.Forms.TextBox tbFilter;
        private System.Windows.Forms.Label label3;
        private System.Windows.Forms.CheckBox cbFatal;
        private System.Windows.Forms.CheckBox cbError;
        private System.Windows.Forms.CheckBox cbWarn;
        private System.Windows.Forms.CheckBox cbInfo;
        private System.Windows.Forms.CheckBox cbDebug;
        private System.Windows.Forms.CheckBox cbTrace;
        private System.Windows.Forms.TabControl tcEntryDetails;
        private System.Windows.Forms.TabPage tpFullMessage;
        private System.Windows.Forms.TabPage tpStacktrace;
        private System.Windows.Forms.Panel panel3;
        private System.Windows.Forms.Label label1;
        private System.Windows.Forms.TextBox tbStackDetails;
        private System.Windows.Forms.Splitter splitter2;
        private System.Windows.Forms.Label label2;
        private System.Windows.Forms.ListView lvCategories;
        private System.Windows.Forms.ColumnHeader cbCategory;
        private System.Windows.Forms.Button btnClearList;
        private System.Windows.Forms.CheckBox cbAutoScroll;
        private System.Windows.Forms.ImageList imageList1;
        private System.Windows.Forms.OpenFileDialog dlgBrowseToLocalSource;
        private System.Windows.Forms.ContextMenuStrip cmDateFilter;
        private System.Windows.Forms.ToolStripMenuItem asdToolStripMenuItem;
        private System.Windows.Forms.ToolStripMenuItem qweToolStripMenuItem;
    }
}
