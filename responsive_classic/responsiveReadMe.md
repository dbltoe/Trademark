# Trademark Files for the Zen Cart responsive_Classic Template
**Adds a Trademark to the Main Page and Mobile Nav Bars**

This has three folders: one for stores using version 1.5.7d of Zen Cart, one for version 1.5.8, and one for 2.0.0 or newer.  This is due to the change in language file formats in 1.5.8 and the addition of FontAwesome icons in version 2.0.0 and later.

NOTE:  If you have not yet cloned your bootstrap or responsive_classic template, we recommend you do that before you install this mod.  Get lat9's excellent [Clone a Template](https://www.zen-cart.com/downloads.php?do=file&id=2087) to do the job.

Pick the folder for your version of Zen Cart.

If you have already cloned either template, then the YOUR_TEMPLATE folder will be the clone you have made of the original template.

There is one new file and one core file overwrite for each version.  With the tpl.header.php files, we are providing updated files for each version that have some important changes.  Carefully merge your template's existing file to ensure a smooth process.

There is one new language file for 1.5.7d and one for 1.5.8a or newer.  This is due to the changes in language defines which started with 1.5.8.  The files do not overwrite any core files and can be simply added to your current files. You will need to change HEADER_TITLE_TRADEMARK in the language file to reflect the company name you want to appear on the navBar.  NOTE:  Since it's a define, special characters like & do not require special coding.

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

Note the capital M in trade**M**ark for the class definition.  This is not a typo but used just in case your template already has a trademark class in its CSS.

You will need to adjust the color, font-size, and font-weight to meet your specific styling needs.

As always, no warranty or guarantee is applied or implied.

**ALWAYS MAKE BACKUPS BEFORE ADDING/EDITING ANY MOD.**
