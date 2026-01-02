# Trademark Files for the Zen Cart Responsive_Classic Template
## Adds a Trademark to the Main Page and Mobile Nav Bars

**NOTE:**  If you have not yet cloned your responsive_classic template, we recommend you do that before you install this mod.  Get lat9's excellent [Clone a Template](https://www.zen-cart.com/downloads.php?do=file&id=2087) to do the job.

### Installation
;This has three folders: one for stores using version 1.5.7d of Zen Cart, one for version 1.5.8, and one for 2.0.0 or newer.  This is due to the addition of FontAwesome icons in version 2.0.0 and later.

Pick the folder for your version of Zen Cart.

If you have already cloned either template, then the YOUR_TEMPLATE folder will be the clone you have made of the original template.

There is one core file overwrite for each version.  With the tpl.header.php files, we are providing updated files for each version that have some important changes.  If you have made any changes to your version, carefully merge your template's existing file to ensure a smooth process.

### CSS Changes
For those using responsive_classic or its clone, we recommend creating includes/templates/YOUR_TEMPLATE/css/stylesheet_zcustom.css and adding the CSS code for this mod to it.

The element that needs to be added to your particular CSS file for all versions of this mod is:
`.tradeMark {
float:left;
margin:0;
padding:0;
color: #FFFFFF;
font-size: 1em;
font-weight:normal;
}`

Note the capital M in trade**M**ark for the class definition.  This is not a typo, but is used just in case your template already has a trademark class in its CSS.

You will need to adjust the color, font-size, and font-weight to meet your specific styling needs while ensuring you meet [accessibility contrast standards](https://docs.zen-cart.com/user/accessibility/accessibility/#what-is-accessibility).

### Legal Stuff
As always, no warranty or guarantee is applied or implied.

**ALWAYS MAKE BACKUPS BEFORE ADDING/EDITING ANY MOD.**
