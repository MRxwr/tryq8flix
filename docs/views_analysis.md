# Views & Index Analysis

## **1. Root `index.php` Analysis**
- **Location**: `c:\Users\nasse\OneDrive\Documents\GitHub\tryq8flix\index.php`
- **Content**:
  ```php
  <?php 
  require_once("../admin/includes/config.php");
  require_once("../admin/includes/functions.php");
  require_once("../../templates/simple_html_dom.php");

  // get viewed page from pages folder \\
  if( isset($_GET["v"]) && searchFile("views","blade{$_GET["v"]}.php") ){
      require_once("views/".searchFile("views","blade{$_GET["v"]}.php"));
  }else{
      echo dataOutput(array("msg" => "404 view Not Found"));die();
  }
  ?>
  ```
- **Issues Identified**:
  - **Incorrect Paths**: The `require_once` paths (`../admin/...`, `../../templates/...`) suggest this file is expected to be in a subdirectory (e.g., `api/` or `try2/`), not in the project root.
  - **Duplicate Logic**: This file appears to be a copy of `api/index.php` but modified to look for `views` instead of `api/views`.

## **2. Views Folder Analysis** (`views/`)
- **Location**: `c:\Users\nasse\OneDrive\Documents\GitHub\tryq8flix\views\`
- **Status**: **All files in this directory appear to be empty.**
- **Files Checked**:
  - `bladeForget.php` (Empty)
  - `bladeHome.php` (Empty)
  - `bladeLiveMatchesList.php` (Empty)
  - `bladeLogin.php` (Empty)
  - `bladeLogut.php` (Empty)
  - `bladeMore.php` (Empty)
  - `bladeProfile.php` (Empty)
  - `bladeSearch.php` (Empty)
  - `bladeServers.php` (Empty)

## **3. Alternative Frontend Location (`try2/`)**
- The active frontend development seems to be located in the `try2/` directory.
- **Key Files**:
  - `try2/default.php`: Main entry point?
  - `try2/liveMatches.php`: Handles live match display, integrating with `api/views/apiLive.php`.
- **Path Anomaly**: Files in `try2/` reference `admin/includes/config.php` directly (e.g., `require("admin/includes/config.php")`). Given the workspace structure where `admin` is a sibling of `try2`, this path would typically fail unless `try2` is the document root or `admin` is symlinked/copied inside `try2`.

## **Conclusion**
The `views/` folder at the root seems to be a placeholder or an abandoned structure. The actual frontend logic appears to reside in `try2/` or is intended to be served via the API endpoints documented previously. The root `index.php` is currently non-functional due to incorrect relative paths.
