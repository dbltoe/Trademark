# Trademark Files for the Zen Cart responsive_Classic Template
**Adds a Trademark to the Main Page and Mobile Nav Bars**

This has two folders, one for stores using version 1.5.7d of Zen Cart and the other for 1.5.8a or newer.  This is due to the change in language file formats in 1.5.8 and later.

NOTE:  If you have not yet cloned your bootstrap or responsive_classic template, we recommend you do that before you install this mod.  Get lat9' excellent [Clone a Template](https://www.zen-cart.com/downloads.php?do=file&id=2087) to do the job.

Pick the folder for your version of Zen Cart.

If you have already cloned either template, then the YOUR_TEMPLATE folder will be the clone you have made of the original template.

NOTE: Although this mod works with the 2.0.0 beta as of 5 Oct 2023, beta files should only be used in a test environment and the procedures listed here for 2.0.0 may change upon its final release.

There is one new file and one core file overwrite for each version.  With the tpl.header.php files, we are providing updated files for each version that have some important changes.  Carefully merge your template's existing file to ensure a smooth process.

For BOTH templates using version 1.5.7d, the includes/languages/english/extra_definitions/trademark_definitions.php does not overrite any file BUT, you will need to modify line 14 to set the HEADER_TITLE_TRADEMARK to the actual name of your company.

The language files are designed to just drop in and not affect core files.

For those using responsive_classic or its clone, we recommend the creation of a stylesheet_zcustom.css and adding the CSS code for this mod.

The element that needs to be added to your particular CSS file for all versions of this mod is:
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
