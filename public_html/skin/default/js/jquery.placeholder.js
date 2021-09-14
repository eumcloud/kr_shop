// Some from http://www.morethannothing.co.uk/wp-content/uploads/2010/01/placeholder.js
// some from http://plugins.jquery.com/files/jquery-placeholder-0.1.js.txt

/*START:plugin */
(function($){  
  
  $.fn.placeholder = function(){   
    
    function valueIsPlaceholder(input){
      return ($(input).val() == $(input).attr("placeholder"));
    }
    return this.each(function() {  
  
      $(this).find(":input").each(function(){
      
        if($(this).attr("type") == "password"){ // <label id="code.placeholder.password" />
          
          var new_field = $("<input type='text'>");
          new_field.attr("rel", $(this).attr("id"));
          new_field.attr("value", $(this).attr("placeholder"));
          $(this).parent().append(new_field);
          new_field.hide();
          
          function showPasswordPlaceHolder(input){
            if( $(input).val() == "" || valueIsPlaceholder(input) ){ 
              $(input).hide();
              $('input[rel=' + $(input).attr("id") + ']').show();
            };
          };
          
          new_field.focus(function(){
            $(this).hide();
            $('input#' + $(this).attr("rel")).show().focus();
          });

          $(this).blur(function(){
             showPasswordPlaceHolder(this, false);
          });

          showPasswordPlaceHolder(this); 
        
        }else{
          
          // Replace the value with the placeholder text. 
          // optional reload parameter solves FF and
          // IE caching values on fields.
          function showPlaceholder(input, reload){
            if( $(input).val() == "" || 
              ( reload && valueIsPlaceholder(input) ) ){ //<label id="code.js.placeholder.reload" />
                $(input).val($(input).attr("placeholder"));
              }
          };
          
          $(this).focus(function(){
            if($(this).val() == $(this).attr("placeholder")){
              $(this).val("");
            };
          });

          $(this).blur(function(){
             showPlaceholder($(this), false)
          });
          
          
          showPlaceholder(this, true); // <label id="code.js.placeholder.reload.call" />
        };
      });
      
      // Prevent forms from submitting default values
      $(this).submit(function(){  // <label id="prevent.values.from.submit"/>
        $(this).find(":input").each(function(){
          if($(this).val() == $(this).attr("placeholder")){
            $(this).val("");
          }          
        });
      });
      
    });  
  };
  
})(jQuery); 
/*END:plugin */

