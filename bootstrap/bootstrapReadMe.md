# Trademark Files for the Zen Cart Bootstrap Template
## Adds a Trademark to the Main Page and Mobile Nav Bars

NOTE:  If you have not yet cloned your bootstrap template, we recommend cloning it before installing this mod.  Get lat9's excellent [Clone a Template](https://www.zen-cart.com/downloads.php?do=file&id=2087) to do the job.

### Core File Additions
Since ZCA Bootstrap is constantly improving, we are providing code to be added in specific places in the ZCA Bootstrap files. The changes in the includes/templates/YOUR_TEMPLATE/common/tpl.header.php (YOUR_TEMPLATE would be your cloned bootstrap folder) now include the automatic inclusion of the STORE_NAME, and the changes for your version of ZCA Bootstrap are as follows:

**For bootstrap versions 3.3.0 to 3.4.1**
Replace the blank line after `<nav class="navbar fixed-top mx-3 navbar-expand-lg rounded-bottom">` with `<p class="tradeMark"><?php echo STORE_NAME; ?></p>`.

**For bootstrap versions 3.5.0 to 3.6.0**
Add a new line 28 after `<nav class="navbar fixed-top mx-3 navbar-expand-lg rounded-bottom" aria-label="<?php echo TEXT_HEADER_ARIA_LABEL_NAVBAR; ?>">` and add `<p class="tradeMark"><?php echo STORE_NAME; ?></p>`

**For bootstrap versions 3.6.1 to 3.6.5**
Add a new line 26 after `<nav class="navbar fixed-top mx-3 navbar-expand-lg rounded-bottom" aria-label="<?php echo TEXT_HEADER_ARIA_LABEL_NAVBAR; ?>">` and add `<p class="tradeMark"><?php echo STORE_NAME; ?></p>`

**For bootstrap versions 3.7.0 to 3.7.8**
**NOTE: These versions DO NOT SUPPORT Zen Cart versions before 1.5.8.**
Add a new line 26 after `<nav class="navbar fixed-top mx-3 navbar-expand-lg rounded-bottom" aria-label="<?php echo TEXT_HEADER_ARIA_LABEL_NAVBAR; ?>">` and add `<p class="tradeMark"><?php echo STORE_NAME; ?></p>`

### Site Specific CSS Changes
For Bootstrap, we recommend renaming or copying includes/templates/YOUR_TEMPLATE/css/dist-site_specific_styles.php to site_specific_styles.php.  Keeping your custom CSS code there makes troubleshooting and updating much easier.

The element that needs to be added to your site_specific_styles.php file for all versions of this mod is:
`.tradeMark {
float:left;
margin:0;
padding:0;
color: #FFFFFF;
font-size: 1em;
font-weight:normal;
}`

Note the capital M in trade**M**ark for the class definition.  This is not a typo, but used just in case your template already has a trademark class in its CSS.

You will need to adjust the color, font-size, and font-weight to meet your specific styling needs while maintaining [accessibility standards](https://docs.zen-cart.com/user/accessibility/accessibility/#what-is-accessibility).

### Legal Stuff
As always, no warranty or guarantee is applied or implied.

**ALWAYS MAKE BACKUPS BEFORE ADDING/EDITING ANY MOD.**







