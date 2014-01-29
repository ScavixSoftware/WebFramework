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
    partial class Form1
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
            if (disposing && (components != null))
            {
                components.Dispose();
            }
            base.Dispose(disposing);
        }

        #region Windows Form Designer generated code

        /// <summary>
        /// Required method for Designer support - do not modify
        /// the contents of this method with the code editor.
        /// </summary>
        private void InitializeComponent()
        {
            System.ComponentModel.ComponentResourceManager resources = new System.ComponentModel.ComponentResourceManager(typeof(Form1));
            this.tcFileViews = new System.Windows.Forms.TabControl();
            this.tabPage1 = new System.Windows.Forms.TabPage();
            this.dlgOpenFile = new System.Windows.Forms.OpenFileDialog();
            this.toolStrip1 = new System.Windows.Forms.ToolStrip();
            this.btnOpen = new System.Windows.Forms.ToolStripSplitButton();
            this.asdToolStripMenuItem = new System.Windows.Forms.ToolStripMenuItem();
            this.btnClose = new System.Windows.Forms.ToolStripButton();
            this.statusStrip1 = new System.Windows.Forms.StatusStrip();
            this.labExceptions = new System.Windows.Forms.ToolStripStatusLabel();
            this.toolStripStatusLabel1 = new System.Windows.Forms.ToolStripStatusLabel();
            this.btnSelectedViewer = new System.Windows.Forms.ToolStripDropDownButton();
            this.itemUltraEdit = new System.Windows.Forms.ToolStripMenuItem();
            this.itemNetbeans = new System.Windows.Forms.ToolStripMenuItem();
            this.tcFileViews.SuspendLayout();
            this.toolStrip1.SuspendLayout();
            this.statusStrip1.SuspendLayout();
            this.SuspendLayout();
            // 
            // tcFileViews
            // 
            this.tcFileViews.Appearance = System.Windows.Forms.TabAppearance.FlatButtons;
            this.tcFileViews.Controls.Add(this.tabPage1);
            this.tcFileViews.Dock = System.Windows.Forms.DockStyle.Fill;
            this.tcFileViews.DrawMode = System.Windows.Forms.TabDrawMode.OwnerDrawFixed;
            this.tcFileViews.Font = new System.Drawing.Font("Microsoft Sans Serif", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.tcFileViews.ItemSize = new System.Drawing.Size(150, 21);
            this.tcFileViews.Location = new System.Drawing.Point(0, 25);
            this.tcFileViews.Name = "tcFileViews";
            this.tcFileViews.Padding = new System.Drawing.Point(16, 3);
            this.tcFileViews.SelectedIndex = 0;
            this.tcFileViews.ShowToolTips = true;
            this.tcFileViews.Size = new System.Drawing.Size(894, 486);
            this.tcFileViews.TabIndex = 1;
            this.tcFileViews.DrawItem += new System.Windows.Forms.DrawItemEventHandler(this.tcFileViews_DrawItem);
            this.tcFileViews.Selected += new System.Windows.Forms.TabControlEventHandler(this.tcFileViews_Selected);
            this.tcFileViews.MouseUp += new System.Windows.Forms.MouseEventHandler(this.tabPage1_MouseUp);
            // 
            // tabPage1
            // 
            this.tabPage1.Font = new System.Drawing.Font("Microsoft Sans Serif", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.tabPage1.Location = new System.Drawing.Point(4, 25);
            this.tabPage1.Name = "tabPage1";
            this.tabPage1.Padding = new System.Windows.Forms.Padding(3);
            this.tabPage1.Size = new System.Drawing.Size(886, 457);
            this.tabPage1.TabIndex = 0;
            this.tabPage1.Text = "tabPage1";
            this.tabPage1.UseVisualStyleBackColor = true;
            // 
            // dlgOpenFile
            // 
            this.dlgOpenFile.FileName = "openFileDialog1";
            this.dlgOpenFile.Multiselect = true;
            // 
            // toolStrip1
            // 
            this.toolStrip1.Items.AddRange(new System.Windows.Forms.ToolStripItem[] {
            this.btnOpen,
            this.btnClose});
            this.toolStrip1.Location = new System.Drawing.Point(0, 0);
            this.toolStrip1.Name = "toolStrip1";
            this.toolStrip1.Size = new System.Drawing.Size(894, 25);
            this.toolStrip1.TabIndex = 3;
            this.toolStrip1.Text = "toolStrip1";
            // 
            // btnOpen
            // 
            this.btnOpen.DropDownItems.AddRange(new System.Windows.Forms.ToolStripItem[] {
            this.asdToolStripMenuItem});
            this.btnOpen.Image = ((System.Drawing.Image)(resources.GetObject("btnOpen.Image")));
            this.btnOpen.ImageTransparentColor = System.Drawing.Color.Magenta;
            this.btnOpen.Name = "btnOpen";
            this.btnOpen.Size = new System.Drawing.Size(77, 22);
            this.btnOpen.Text = "Open...";
            this.btnOpen.ButtonClick += new System.EventHandler(this.btnOpen_Click);
            // 
            // asdToolStripMenuItem
            // 
            this.asdToolStripMenuItem.Name = "asdToolStripMenuItem";
            this.asdToolStripMenuItem.Size = new System.Drawing.Size(92, 22);
            this.asdToolStripMenuItem.Text = "asd";
            // 
            // btnClose
            // 
            this.btnClose.Image = ((System.Drawing.Image)(resources.GetObject("btnClose.Image")));
            this.btnClose.ImageTransparentColor = System.Drawing.Color.Magenta;
            this.btnClose.Name = "btnClose";
            this.btnClose.Size = new System.Drawing.Size(56, 22);
            this.btnClose.Text = "Close";
            this.btnClose.Click += new System.EventHandler(this.btnClose_Click);
            // 
            // statusStrip1
            // 
            this.statusStrip1.Items.AddRange(new System.Windows.Forms.ToolStripItem[] {
            this.labExceptions,
            this.toolStripStatusLabel1,
            this.btnSelectedViewer});
            this.statusStrip1.Location = new System.Drawing.Point(0, 511);
            this.statusStrip1.Name = "statusStrip1";
            this.statusStrip1.ShowItemToolTips = true;
            this.statusStrip1.Size = new System.Drawing.Size(894, 22);
            this.statusStrip1.TabIndex = 4;
            this.statusStrip1.Text = "statusStrip1";
            // 
            // labExceptions
            // 
            this.labExceptions.Image = global::WdfTracer.Properties.Resources.success;
            this.labExceptions.Name = "labExceptions";
            this.labExceptions.Size = new System.Drawing.Size(88, 17);
            this.labExceptions.Text = "0 Exceptions";
            this.labExceptions.ToolTipText = "Click to open Logfile";
            this.labExceptions.Click += new System.EventHandler(this.labExceptions_Click);
            // 
            // toolStripStatusLabel1
            // 
            this.toolStripStatusLabel1.Name = "toolStripStatusLabel1";
            this.toolStripStatusLabel1.Size = new System.Drawing.Size(636, 17);
            this.toolStripStatusLabel1.Spring = true;
            // 
            // btnSelectedViewer
            // 
            this.btnSelectedViewer.DropDownItems.AddRange(new System.Windows.Forms.ToolStripItem[] {
            this.itemUltraEdit,
            this.itemNetbeans});
            this.btnSelectedViewer.Image = global::WdfTracer.Properties.Resources.none;
            this.btnSelectedViewer.ImageTransparentColor = System.Drawing.Color.Magenta;
            this.btnSelectedViewer.Name = "btnSelectedViewer";
            this.btnSelectedViewer.Size = new System.Drawing.Size(124, 20);
            this.btnSelectedViewer.Text = "No viewer found";
            this.btnSelectedViewer.Click += new System.EventHandler(this.btnSelectedViewer_Click);
            // 
            // itemUltraEdit
            // 
            this.itemUltraEdit.Name = "itemUltraEdit";
            this.itemUltraEdit.Size = new System.Drawing.Size(152, 22);
            this.itemUltraEdit.Tag = "ultraedit";
            this.itemUltraEdit.Text = "UltraEdit";
            // 
            // itemNetbeans
            // 
            this.itemNetbeans.Name = "itemNetbeans";
            this.itemNetbeans.Size = new System.Drawing.Size(152, 22);
            this.itemNetbeans.Tag = "netbeans";
            this.itemNetbeans.Text = "NetBeans";
            // 
            // Form1
            // 
            this.AllowDrop = true;
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 13F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.ClientSize = new System.Drawing.Size(894, 533);
            this.Controls.Add(this.tcFileViews);
            this.Controls.Add(this.statusStrip1);
            this.Controls.Add(this.toolStrip1);
            this.Icon = ((System.Drawing.Icon)(resources.GetObject("$this.Icon")));
            this.Name = "Form1";
            this.StartPosition = System.Windows.Forms.FormStartPosition.Manual;
            this.Text = "WdfTracer";
            this.FormClosing += new System.Windows.Forms.FormClosingEventHandler(this.Form1_FormClosing);
            this.Load += new System.EventHandler(this.Form1_Load);
            this.LocationChanged += new System.EventHandler(this.Form1_LocationChanged);
            this.DragDrop += new System.Windows.Forms.DragEventHandler(this.Form1_DragDrop);
            this.DragEnter += new System.Windows.Forms.DragEventHandler(this.Form1_DragEnter);
            this.Resize += new System.EventHandler(this.Form1_Resize);
            this.tcFileViews.ResumeLayout(false);
            this.toolStrip1.ResumeLayout(false);
            this.toolStrip1.PerformLayout();
            this.statusStrip1.ResumeLayout(false);
            this.statusStrip1.PerformLayout();
            this.ResumeLayout(false);
            this.PerformLayout();

        }

        #endregion

        private System.Windows.Forms.TabControl tcFileViews;
        private System.Windows.Forms.OpenFileDialog dlgOpenFile;
        private System.Windows.Forms.TabPage tabPage1;
        private System.Windows.Forms.ToolStrip toolStrip1;
        private System.Windows.Forms.ToolStripSplitButton btnOpen;
        private System.Windows.Forms.ToolStripMenuItem asdToolStripMenuItem;
        private System.Windows.Forms.ToolStripButton btnClose;
        private System.Windows.Forms.StatusStrip statusStrip1;
        private System.Windows.Forms.ToolStripStatusLabel labExceptions;
        private System.Windows.Forms.ToolStripDropDownButton btnSelectedViewer;
        private System.Windows.Forms.ToolStripMenuItem itemUltraEdit;
        private System.Windows.Forms.ToolStripMenuItem itemNetbeans;
        private System.Windows.Forms.ToolStripStatusLabel toolStripStatusLabel1;

    }
}

