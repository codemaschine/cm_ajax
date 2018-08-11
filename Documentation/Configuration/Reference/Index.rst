.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


.. _configuration-reference:

Reference
---------

Possible subsections: Reference (TypoScript)

=================  ==========  =======================================================  ========
Property:          Data type:  Description:                                             Default:
=================  ==========  =======================================================  ========
allWrap /+stdWrap  wrap        Wraps the whole item
-----------------  ----------  -------------------------------------------------------  --------
wrapItemAndSub     wrap        Wraps the whole item and any submenu concatenated to
                               it.
-----------------  ----------  -------------------------------------------------------  --------
subst_elementUid   boolean     If set, all appearances of the string '{elementUid}' in
                               the total element html-code (after wrapped in .allWrap}
                               is substituted with the uid number of the menu item.

                               This is useful if you want to insert an identification
                               code in the HTML in order to manipulate properties with
                               JavaScript.
-----------------  ----------  -------------------------------------------------------  --------
RO_chBgColor       string      If property RO is set (see below) then you can set this
                               property to a certain set of parameters which will
                               allow you to change the background color of eg. the
                               tablecell when the mouse rolls over you text-link.

                               **Syntax:**

                               [over-color] | [out-color] | [id-prefix]

                               **Example:**

                               page = PAGE

                               page.typeNum = 0

                               page.10 = HMENU

                               page.10.wrap = <table border=1>|</table>

                               page.10.1 = TMENU

                               page.10.1.NO {

                               allWrap = <tr><td valign=top id="1tmenu{elementUid}"
                               style="background:#eeeeee;">|</td></tr>

                               subst_elementUid = 1

                               RO_chBgColor = #cccccc | #eeeeee | 1tmenu

                               RO = 1

                               }

                               This example will start out with the table cells in
                               #eeeeee and change them to #cccccc (and back) when
                               rolled over. The “1tmenu” string is a unique id for
                               the menu items. You may not need it (unless the same
                               menu items are more than once on a page), but the
                               important thing is that the id of the table cell has
                               the exact same label before the {elementUid} (red
                               marks). The other important thing is that you DO set a
                               default background color for the cell with the
                               style-attribute (blue marking). If you do not, Mozilla
                               browsers will behave a little strange by not capturing
                               the mouseout event the first time it's triggered.
=================  ==========  =======================================================  ========

[tsref:(cObject).TEST]

.. toctree::
    :maxdepth: 2
    :titlesonly:

    Example/Index
