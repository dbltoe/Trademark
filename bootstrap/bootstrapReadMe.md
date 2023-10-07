# Trademark Files for the Zen Cart Bootstrap Template
**Adds a Trademark to the Main Page and Mobile Nav Bars**

This has two folders, one for stores using version 1.5.7d of Zen Cart and the other for 1.5.8a or newer.  This due to the change in language file formats in 1.5.8 and later.

NOTE:  If you have not yet cloned your bootstrap or responsive_classic template, we recommend you do that before you install this mod.  Get lat9' excellent [Clone a Template](https://www.zen-cart.com/downloads.php?do=file&id=2087) to do the job.

Pick the folder for your version of Zen Cart.

NOTE: Although this mod works with the 2.0.0 beta as of 5 Oct 2023, beta files should only be used in a test environment and the procedures listed here for 2.0.0 may change upon its final release.

There is one new language file for 1.5.7d and one for 1.5.8a or newer.  This is due to the changes in language defines which started with 1.5.8.  The files do not overwrite any corefiles and can be simply added to your current files.

The changes in the includes/templates/YOUR_TEMPLATE/common/tpl.header.php are as follows:

**For bootstrap versions 3.3.0 to 3.4.1**
Replace the blank line after `<nav class="navbar fixed-top mx-3 navbar-expand-lg rounded-bottom">` with `<p class="tradeMark"><?php echo HEADER_TITLE_TRADEMARK; ?></p>`.

**For bootstrap versions 3.5.0 to 3.6.0**
Add a new line 28 after `<nav class="navbar fixed-top mx-3 navbar-expand-lg rounded-bottom" aria-label="<?php echo TEXT_HEADER_ARIA_LABEL_NAVBAR; ?>">` and add `<p class="tradeMark"><?php echo HEADER_TITLE_TRADEMARK; ?></p>`

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

Note the capital M in trademark for the class definition.  This is not a typo but used just in case your template already has a trademark class in its CSS.

You will need to adjust the color, font-size, and font-weight to meet your specific styling needs.

As always, no warranty or guarantee is applied or implied.

**ALWAYS MAKE BACKUPS BEFORE ADDING/EDITING ANY MOD.**
