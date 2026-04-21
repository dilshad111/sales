[Setup]
AppId={{8B841F9B-B128-403E-A00D-4CD8381C65E1}
AppName=Astola ERP
AppVersion=1.0.0
AppPublisher=Astola Technology
DefaultDirName=C:\xampp\htdocs\Sales
DefaultGroupName=Astola ERP
AllowNoIcons=yes
OutputDir=C:\Users\HP\Desktop
OutputBaseFilename=Astola_ERP_Setup
Compression=lzma
SolidCompression=yes
WizardStyle=modern
DisableDirPage=no
SetupIconFile=C:\xampp\htdocs\Sales\prerequisites\astola erp.ico
UninstallDisplayIcon={app}\AstolaLauncher.exe

[Languages]
Name: "english"; MessagesFile: "compiler:Default.isl"

[Files]
; Package all files recursively, excluding development directories.
Source: "C:\xampp\htdocs\Sales\*"; DestDir: "{app}"; Flags: ignoreversion recursesubdirs createallsubdirs; Excludes: "node_modules\*, .git\*, AstolaSetup.iss, LaunchERP.cs"

[Icons]
Name: "{autodesktop}\ASTOLA ERP"; Filename: "{app}\AstolaLauncher.exe"; WorkingDir: "{app}"
Name: "{group}\ASTOLA ERP"; Filename: "{app}\AstolaLauncher.exe"; WorkingDir: "{app}"
Name: "{group}\Uninstall ASTOLA ERP"; Filename: "{uninstallexe}"

[Run]
; Auto-import the database during installation. Using XAMPP's default path.
Filename: "C:\xampp\mysql\bin\mysql.exe"; Parameters: "-u root -e ""CREATE DATABASE IF NOT EXISTS sales_db; USE sales_db; source {app}\sales_dump.sql;"""; Flags: waituntilterminated runhidden; StatusMsg: "Importing Application Database..."
; Prerequisite Installers
Filename: "{app}\prerequisites\xampp-installer.exe"; Description: "Install XAMPP Server (IMPORTANT: Choose a directory like C:\xampp2 if C:\xampp exists)"; Flags: postinstall skipifsilent shellexec waituntilterminated unchecked
Filename: "{app}\prerequisites\node-installer.msi"; Description: "Install Node.js Framework"; Flags: postinstall skipifsilent shellexec waituntilterminated unchecked
; Give the user the option to launch the app after setup finishes.
Filename: "{app}\AstolaLauncher.exe"; Description: "Launch ASTOLA ERP System"; Flags: nowait postinstall skipifsilent
