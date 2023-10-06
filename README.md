# Trademark
Adds a Trademark to the Main Page and Mobile Nav Bars

A recent client had a requirement for their trademark to be plainly shown on all pages of their Zen Cart website. The best answer was to add it to the navMainWrapper in front of the default "Home" declaration for the desktop view and immediately after the "hamburger" on the mobile/tablet menu.

This mod has  a folder for the responsive_classic template files needed for 1.5.7d, 1.58a, and 2.0.0 beta versions of Zen Cart.

The bootstrap folder contains files needed to update the bootstrap template for 1.5.7d, 1.58a, and 2.0.0 beta versions of Zen Cart.

NOTE:  If you have not yet cloned your bootstrap or responsive_classic template, we recommend you do that before you install this mod.  Get lat9' excellent [Clone a Template](https://www.zen-cart.com/downloads.php?do=file&id=2087) to do the job.

Pick the folder for your template and then the sub-folder for your version of Zen Cart.

If you have already cloned either template, then the YOUR_TEMPLATE folder will be the clone you have made of the original template.

NOTE: the 2.0.0 version of both templates included with this mod is based on the beta as of 5 October 2023 and should not be used in other than a test environment.

There is one new file and one core file overwrite for each template and version.  A common CSS element provides the styling for the mod.

For BOTH templates using version 1.5.7d, the includes/languages/english/extra_definitions/trademark_definitions.php does not overrite any file BUT, you will need to modify line 14 to set the HEADER_TITLE_TRADEMARK to the actual name of your company.

For BOTH templates using version 1.5.8a and 2.0.0 beta, the includes/languages/english/extra_definitions/lang.trademark.php does not overrite any file BUT, you will need to modify line 3 to set the HEADER_TITLE_TRADEMARK to the actual name of your company.

NOTE:  If you have customized your templates /common/tpl_header, you will need to properly merge the included file with that on your current site.

Each of the includes/templates/YOUR_TEMPLATE/common/tpl_header.php files supplied for responsive_classic templates is a replacement for the original tpl_header.php that was shipped with that version.  The files have updates/corrections along with the code needed for the inclusion of the trademark.

For those using responsive_classic or its clone, we recommend the creation of a stylesheet_zcustom.css and adding the CSS code for this mod.

For those using bootstrap or its clone, if you have not already copied the dist-site_specific_styles.php file to  site_specific_styles.php, do that first.  Then add the CSS code for this mod.

The element that needs to be added to your particular CSS file for all versions of this mod is: `.tradeMark {float:left;margin:0;padding:0;}`

Note the capital M in trademark.  This is not a typo but used just in case your template already has a trademark class in its CSS.

This results in text with the default color assigned to the body of your template (generally white/#FFFFFF).  If you want/need a different color for the text, simply add `color:#******;` to the CSS where ****** is the hexadecimal code for the desired color.  Keep color contrast of at least 4.5 to 1 when choosing your text color.
