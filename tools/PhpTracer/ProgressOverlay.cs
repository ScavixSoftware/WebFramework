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
using System.ComponentModel;
using System.Drawing;
using System.Data;
using System.Linq;
using System.Text;
using System.Windows.Forms;

namespace PhpTracer
{
    public delegate void ProgressDelegate(long min, long max, long position);
    public delegate void CancelledDelegate(ProgressOverlay sender);

    public partial class ProgressOverlay : UserControl
    {
        public event CancelledDelegate OnCancelled;

        public ProgressOverlay()
        {
            InitializeComponent();
            SetStyle(ControlStyles.SupportsTransparentBackColor, true);
            BackColor = Color.Transparent;// Color.FromArgb(100, Color.Black);
            Dock = DockStyle.Fill;
        }

        public void SetProgress(long min, long max, long position)
        {
            if (InvokeRequired)
            {
                Invoke(new ProgressDelegate(SetProgress), new object[] { min, max, position });
                return;
            }
            progBar.Minimum = (int)min;
            progBar.Maximum = (int)max;
            progBar.Value = (int)position;

            if (position == max)
                Hide();
            else
            {
                Show();
                ProgressOverlay_Resize(null, null);
                BringToFront();
            }
        }

        private void ProgressOverlay_Resize(object sender, EventArgs e)
        {
            if (Visible)
            {
                panProgress.Left = (Width / 2) - (panProgress.Width / 2);
                panProgress.Top = (Height / 2) - (panProgress.Height / 2);
            }
        }

        private void button1_Click(object sender, EventArgs e)
        {
            if (OnCancelled != null)
                OnCancelled(this);
        }
    }
}
