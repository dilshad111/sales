using System;
using System.Diagnostics;
using System.IO;

class Program {
    static void Main(string[] args) {
        string url = "http://localhost/Sales/public/";
        
        string chromePath = "";
        
        string[] paths = {
            Environment.GetFolderPath(Environment.SpecialFolder.ProgramFiles) + @"\Google\Chrome\Application\chrome.exe",
            Environment.GetFolderPath(Environment.SpecialFolder.ProgramFilesX86) + @"\Google\Chrome\Application\chrome.exe",
            Environment.GetFolderPath(Environment.SpecialFolder.LocalApplicationData) + @"\Google\Chrome\Application\chrome.exe",
            Environment.GetFolderPath(Environment.SpecialFolder.ProgramFilesX86) + @"\Microsoft\Edge\Application\msedge.exe",
            Environment.GetFolderPath(Environment.SpecialFolder.ProgramFiles) + @"\Microsoft\Edge\Application\msedge.exe"
        };
        
        foreach(var p in paths) {
            if(File.Exists(p)) {
                 chromePath = p;
                 break;
            }
        }
        
        try {
            if(!string.IsNullOrEmpty(chromePath)) {
                Process.Start(new ProcessStartInfo() {
                    FileName = chromePath,
                    Arguments = "--app=" + url,
                    UseShellExecute = true
                });
            } else {
                Process.Start(new ProcessStartInfo() {
                    FileName = url,
                    UseShellExecute = true
                });
            }
        } catch {
            Process.Start(new ProcessStartInfo() {
                FileName = url,
                UseShellExecute = true
            });
        }
    }
}
