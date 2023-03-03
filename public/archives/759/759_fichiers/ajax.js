function submitQuickSubscribe(source, container){
   jQuery('.'+container).hide();
   jQuery.post(
   // see tip #1 for how we declare global javascript variables
   MyAjax.ajaxurl,
   {
   // here we declare the parameters to send along with the request
   // this means the following action hooks will be fired:
   // wp_ajax_nopriv_myajax-submit and wp_ajax_myajax-submit
   action : 'quicksubscribe_submit',

   // other parameters can be added along with "action"
   userEmail : document.getElementById(source).value,
   source : source,
   containerDiv: container
   },
   function( response ) {
      jQuery('.'+container).html(response).fadeIn(1000);
   }
   );
}

function checkForEnter (e, source, container) {
     if ((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13)){
         submitQuickSubscribe(source, container);
         return false;
     } 
}

function fadeSubmitIn(elName){
   jQuery('.'+elName).fadeIn(1000);
}

function fadeSubmitOut(elem, elName){
    if( elem.value == '' || elem.value == null){
       jQuery('.'+elName).hide();
    }
 }
