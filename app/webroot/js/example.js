$(function() {    $('.entree').editable('/specials/updateentree', {         id        : 'data[Entree][id]',         name      : 'data[Entree][name]',         type      : 'text',         cancel    : "<a class='button negative' style='font-weight: bold;font-size: small;'><img src='../../img/icons/cancel.png'> Cancel</a>",         submit    : "<button type='submit' class='button positive' style='font-weight: bold;font-size: small;'><img src='../../img/icons/accept.png'> Save</button>",         tooltip   : 'Click to edit the Entree',
         width	   : '250',
         indicator : '<img src="../../img/spinner.gif">'
         });});
$(function() {    $('.description').editable('/specials/updatedescription', {         id        : 'data[Entree][id]',         name      : 'data[Entree][description]',         type      : 'textarea',         cancel    : "<a class='button negative' style='font-weight: bold;font-size: small;'><img src='../../img/icons/cancel.png'> Cancel</a><br>",         submit    : "<button type='submit' class='button positive' style='font-weight: bold;font-size: small;'><img src='../../img/icons/accept.png'> Save</button>",         tooltip   : 'Click to edit the Description',
		 rows	   : '5',
		 width	   : '250px',
         indicator : '<img src="../../img/spinner.gif">'
         });});
$(function() {    $('.soup').editable('/specials/updatesoup', {         id        : 'data[Soup][id]',         name      : 'data[Soup][name]',         type      : 'text',         cancel    : "<a class='button negative' style='font-weight: bold;font-size: small;'><img src='../../img/icons/cancel.png'> Cancel</a><br>",         submit    : "<button type='submit' class='button positive' style='font-weight: bold;font-size: small;'><img src='../../img/icons/accept.png'> Save</button>",         tooltip   : 'Click to edit the Soup',
		 width	   : '150px',
         indicator : '<img src="../../img/spinner.gif">'
         });});