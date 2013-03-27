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
    partial class ProgressOverlay
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

        #region Component Designer generated code

        /// <summary> 
        /// Required method for Designer support - do not modify 
        /// the contents of this method with the code editor.
        /// </summary>
        private void InitializeComponent()
        {
            this.panProgress = new System.Windows.Forms.Panel();
            this.progBar = new System.Windows.Forms.ProgressBar();
            this.labTitle = new System.Windows.Forms.Label();
            this.button1 = new System.Windows.Forms.Button();
            this.panProgress.SuspendLayout();
            this.SuspendLayout();
            // 
            // panProgress
            // 
            this.panProgress.Controls.Add(this.button1);
            this.panProgress.Controls.Add(this.progBar);
            this.panProgress.Controls.Add(this.labTitle);
            this.panProgress.Location = new System.Drawing.Point(3, 3);
            this.panProgress.Name = "panProgress";
            this.panProgress.Size = new System.Drawing.Size(454, 68);
            this.panProgress.TabIndex = 6;
            // 
            // progBar
            // 
            this.progBar.Location = new System.Drawing.Point(16, 29);
            this.progBar.Name = "progBar";
            this.progBar.Size = new System.Drawing.Size(341, 23);
            this.progBar.TabIndex = 1;
            // 
            // labTitle
            // 
            this.labTitle.AutoSize = true;
            this.labTitle.BackColor = System.Drawing.Color.Transparent;
            this.labTitle.Font = new System.Drawing.Font("Microsoft Sans Serif", 9.75F, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.labTitle.Location = new System.Drawing.Point(13, 9);
            this.labTitle.Name = "labTitle";
            this.labTitle.Size = new System.Drawing.Size(101, 16);
            this.labTitle.TabIndex = 0;
            this.labTitle.Text = "Loading file...";
            // 
            // button1
            // 
            this.button1.Location = new System.Drawing.Point(363, 29);
            this.button1.Name = "button1";
            this.button1.Size = new System.Drawing.Size(75, 23);
            this.button1.TabIndex = 2;
            this.button1.Text = "Cancel";
            this.button1.UseVisualStyleBackColor = true;
            this.button1.Click += new System.EventHandler(this.button1_Click);
            // 
            // ProgressOverlay
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 13F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.BackColor = System.Drawing.Color.Transparent;
            this.Controls.Add(this.panProgress);
            this.Name = "ProgressOverlay";
            this.Size = new System.Drawing.Size(507, 167);
            this.Resize += new System.EventHandler(this.ProgressOverlay_Resize);
            this.panProgress.ResumeLayout(false);
            this.panProgress.PerformLayout();
            this.ResumeLayout(false);

        }

        #endregion

        private System.Windows.Forms.Panel panProgress;
        private System.Windows.Forms.ProgressBar progBar;
        private System.Windows.Forms.Label labTitle;
        private System.Windows.Forms.Button button1;
    }
}
