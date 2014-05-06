using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.IO;
using System.Linq;
using System.Text;
using System.Windows.Forms;

namespace WdfTracer
{
    public partial class Form2 : Form
    {
        List<SourceViewer> ItemsList;
        List<SourceViewer> DeleteItems = new List<SourceViewer>();

        public Form2()
        {
            InitializeComponent();
            ItemsList = Program.Viewer;
            listrefresh();
        }

        private void listrefresh()
        {
            listView1.Items.Clear();
            listView1_SelectedIndexChanged(null,null);
            foreach (SourceViewer s in ItemsList)
            {
                ListViewItem item = listView1.Items.Add(s.Name);
                item.Tag = s;
                if (s.Image != null)
                {
                    imageList1.Images.Add(s.Image);
                    item.ImageIndex = imageList1.Images.Count - 1;
                }

            }
            txtCheck();
        }

        private void btnOk_Click(object sender, EventArgs e)
        {
            foreach (ListViewItem item in listView1.Items)
            {
                SourceViewer s = item.Tag as SourceViewer;
                s.Nummer = item.Index;
                s.Save(Program.ViewerSettings);
            }
            foreach (SourceViewer s in DeleteItems)
            {
                s.Delete(Program.ViewerSettings);
                s.DeleteIcon();
            }
            this.DialogResult = DialogResult.OK;
        }

        private void btnCancel_Click(object sender, EventArgs e)
        {
            this.DialogResult = DialogResult.Cancel;
        }

        private void tableLayoutPanel1_Paint(object sender, PaintEventArgs e)
        {

        }

        private void btnItemAdd_Click(object sender, EventArgs e)
        {
            SourceViewer source = new SourceViewer();
            OpenFileDialog openFileDialog1 = new OpenFileDialog();
            openFileDialog1.Filter = ".exe files (*.exe)|*.exe|.com files (*.com)|*.com";
            if (openFileDialog1.ShowDialog() == DialogResult.OK)
            {
                List<SourceViewer> buf = new List<SourceViewer>();
                foreach (ListViewItem temp in listView1.Items)
                    buf.Add(temp.Tag as SourceViewer);
                buf.AddRange(DeleteItems);

                string alias = source.Alias = Path.GetFileNameWithoutExtension(openFileDialog1.FileName);
                bool exists = true;
                int z = 1;
                while (exists)
                {
                    exists = false;
                    foreach (SourceViewer s in buf)
                    {
                        if (alias == s.Alias)
                        {
                            alias = source.Alias + "_" + z;
                            z++;
                            exists = true;
                            break;
                        }
                    }
                }
                source.Alias = alias;
                source.Name = Path.GetFileNameWithoutExtension(openFileDialog1.FileName);
                source.ExeSearchName = Path.GetFileName(openFileDialog1.FileName);
                source.Executable = openFileDialog1.FileName;
                ListViewItem item = listView1.Items.Add(source.Name);
                item.Tag = source;
                imageList1.Images.Add(source.Image);
                item.ImageIndex = imageList1.Images.Count - 1;
                ItemsList.Add(source);
                int m = listView1.Items.Count;
                m --;
                this.listView1.Items[m].Selected = true;
            }
        }

        private void txtbxName_TextChanged(object sender, EventArgs e)
        {
            txtCheck();
        }

        private void txtbxPath_TextChanged(object sender, EventArgs e)
        {
            txtCheck();
        }

        private void txtbxArgument_TextChanged(object sender, EventArgs e)
        {
            txtCheck();
        }

        private void listView1_SelectedIndexChanged(object sender, EventArgs e)
        {
            if (listView1.SelectedItems.Count < 1)
            {
                txtbxName.Text = "";
                txtbxPath.Text = "";
                txtbxArgument.Text = "";
                groupBox1.Enabled = false;
                btnPath.Enabled = false;
                btnItemDelete.Enabled = false;
                btnItemUp.Enabled = false;
                btnItemDown.Enabled = false;
                return;
            }
            else
            {
                groupBox1.Enabled = true;
                btnPath.Enabled = true;
                btnItemDelete.Enabled = true;
                btnItemUp.Enabled = true;
                btnItemDown.Enabled = true;
                SourceViewer s = listView1.SelectedItems[0].Tag as SourceViewer;
                txtbxName.Text = s.Name;
                txtbxPath.Text = s.Executable;
                txtbxArgument.Text = s.ArgumentPattern;
            }
        }

        private void btnPath_Click(object sender, EventArgs e)
        {
            ListViewItem item = listView1.SelectedItems[0];
            SourceViewer source = item.Tag as SourceViewer;
            OpenFileDialog openFileDialog1 = new OpenFileDialog();
            openFileDialog1.Filter = ".exe files (*.exe)|*.exe|.com files (*.com)|*.com";
            if (source.Executable != "") { openFileDialog1.InitialDirectory = Path.GetDirectoryName(source.Executable); }
            if (openFileDialog1.ShowDialog() == DialogResult.OK)
            {
                txtbxPath.Text = openFileDialog1.FileName;
            }
        }

        private void btnItemDelete_Click(object sender, EventArgs e)
        {
            SourceViewer s = listView1.SelectedItems[0].Tag as SourceViewer;
            DeleteItems.Add(s);
            ItemsList.Remove(s);
            listView1.Items.Remove(listView1.SelectedItems[0]);
        }

        private void btnItemUp_Click(object sender, EventArgs e)
        {
            ListViewItem item = listView1.SelectedItems[0];
            if (item.Index < 1)
                return;

            ListViewItem other = listView1.Items[item.Index - 1];
            listView1.Items.Remove(item);
            listView1.Items.Insert(other.Index, item);
        }

        private void btnItemDown_Click(object sender, EventArgs e)
        {
            ListViewItem item = listView1.SelectedItems[0];
            int i = listView1.Items.Count - 2;
            if (item.Index > i)
                return;

            ListViewItem other = listView1.Items[item.Index + 1];
            listView1.Items.Remove(other);
            listView1.Items.Insert(item.Index, other);
        }

        private void btnRename_Click(object sender, EventArgs e)
        {
            ListViewItem item = listView1.SelectedItems[0];
            SourceViewer source = item.Tag as SourceViewer;
            source.DeleteIcon();
            source.Name = txtbxName.Text;
            source.ArgumentPattern = txtbxArgument.Text;
            if (File.Exists(txtbxPath.Text)) 
            {
                source.Executable = txtbxPath.Text;
                source.ExeSearchName = Path.GetFileName(txtbxPath.Text);
                source.Executable = txtbxPath.Text;
                txtCheck();
            }
            listrefresh();
        }

        private void btnDiscard_Click(object sender, EventArgs e)
        {
            SourceViewer s = listView1.SelectedItems[0].Tag as SourceViewer;
                txtbxName.Text = s.Name;
                txtbxPath.Text = s.Executable;
                txtbxArgument.Text = s.ArgumentPattern;
                txtCheck();
        }

        private void txtCheck()
        {
            if (listView1.SelectedItems.Count < 1)
                return;
            SourceViewer s = listView1.SelectedItems[0].Tag as SourceViewer;
            if (txtbxPath.Text != s.Executable || txtbxName.Text != s.Name || txtbxArgument.Text != s.ArgumentPattern)
            {
                
                panel1.Enabled = false;
                btnOk.Enabled = false;
                btnCancel.Enabled = false;
                btnRename.Enabled = true;
                btnDiscard.Enabled = true;
            }
            else
            {
                panel1.Enabled = true;
                btnOk.Enabled = true;
                btnCancel.Enabled = true;
                btnRename.Enabled = false;
                btnDiscard.Enabled = false;
            }

            if (Path.GetExtension(txtbxPath.Text) != ".exe" && Path.GetExtension(txtbxPath.Text) != ".com" || File.Exists(txtbxPath.Text) == false && txtbxPath.Text != "")
            {

                btnRename.Enabled = false;
                txtbxPath.BackColor = Color.Red;
            }
            else
            {
                txtbxPath.BackColor = SystemColors.Window;
            }
        }

        
    }
}
