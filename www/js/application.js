jQuery.ajaxSetup({ 
  'beforeSend': function(xhr) {xhr.setRequestHeader("Accept",
    "text/javascript")} 
})

// Chargify Namespace
var Chargify = {};

// Chargify Product code
Chargify.Product = (function($){
  // "private"

  return {
  }

})(jQuery);

// Settings Panel
Chargify.GatewaySettings = (function($){
  // "private"


  // "public"
  return {
    init: function() {
      // TODO: attach click handlers to submit gateway config forms via AJAX
    }
  }

})(jQuery);

var tmp;

// Chargify Quantity-Based components code
Chargify.MultiBracketPricingComponents = (function($){
  var all_empty_price_brackets = null;
  var all_new_price_brackets = null;
  var add_another_link = null;
  var need_more = null;
  // "private"
  
  var componentType = function(){
    return $('#component_type').val();
  }
  
  var schemeSelect = function(){
    return '#' + componentType() + '_component_pricing_scheme';
  }
  
  var pricePerUnitInput = function(){
    return '#' + componentType() + '_component_unit_price_input';
  }
  
  var unitNameInput = function(){
    return '#' + componentType() + '_component_unit_name';
  }
  
  var findEmpties = function() {
    all_empty_price_brackets = $('#component_price_brackets .price_bracket.empty');
    all_new_price_brackets = $('#component_price_brackets .price_bracket.new_record');
  }
  
  var addRemoveLink = function(price_bracket) {
    remove_link = $('<a href="#">Remove</a>');
    price_bracket.find('li.remove_link').html(remove_link);
    remove_link.click(function(event){
      event.preventDefault();
      clearFields(price_bracket);
      removePriceBracket(price_bracket);
    });
  }
  
  var addPriceBracket = function() {
    var first_empty_price_bracket = all_empty_price_brackets.eq(0);
    first_empty_price_bracket.after(add_another_link);
    first_empty_price_bracket.show();
    first_empty_price_bracket.removeClass('empty');
    findEmpties();

    if (all_empty_price_brackets.length == 0) {
      add_another_link.hide();
      need_more.show();
    }
  }
  
  var removePriceBracket = function(price_bracket) {
    price_bracket.hide();
    price_bracket.addClass('empty');
    add_another_link.after(price_bracket);
    add_another_link.show();
    need_more.hide();
    findEmpties();
  }
  
  var clearFields = function(price_bracket) {
    price_bracket.find('input').each(function(){
      this.value = '';
    });
  }
  
  var updatePricingFields = function() {
    var scheme = $(schemeSelect()).val();
    switch (scheme) {
      case "per_unit":
        $(pricePerUnitInput()).show();
        $('#component_price_brackets').hide();
        break;
      case "tiered":
        $(pricePerUnitInput()).hide();
        $('#component_price_brackets').show();
        break;
      case "volume":
        $(pricePerUnitInput()).hide();
        $('#component_price_brackets').show();
        break;
      case "stairstep":
        $(pricePerUnitInput()).hide();
        $('#component_price_brackets').show();
        break;
      default:
        $(pricePerUnitInput()).hide();
        $('#component_price_brackets').hide();
    }
  }
  
  var propagateUnitName = function() {
    $(pricePerUnitInput() + ' p.inline-hints').text("per " + currentUnitName());
    if ($(schemeSelect()).val() == 'stairstep') {
      $('.price_bracket .bracket_price').next('p.inline-hints').text("per step");
    }
    else {
      $('.price_bracket .bracket_price').next('p.inline-hints').text("per " + currentUnitName());
    }
  }
  
  var currentUnitName = function() {
    var unit_name = $(unitNameInput()).val();
    if (unit_name && (unit_name.length > 0)) {
      return unit_name;
    }
    else {
      return "unit";
    }
  }
  
	return {
	  init: function() {
	    
	    // Activate delete checkboxes
	    $('#component_price_brackets .price_bracket input.delete_checkbox').change(function(){
	      if ($(this).is(':checked')) {
	        $(this).closest('.price_bracket').find('input[type=text]').disable();
	      } else {
	        $(this).closest('.price_bracket').find('input[type=text]').enable();
	      }
	    });
	    
	    // Empties
	    findEmpties();
	    add_another_link = $('<p><a href="#">+ Add another Price Bracket</a></p>');
	    need_more = $('#need_more');
	    
	    all_new_price_brackets.each(function(){
	      addRemoveLink($(this));
	    });
	    all_empty_price_brackets.each(function(){
	      $(this).hide();
	    });
	    need_more.hide()
	    add_another_link.click(function(event) {
	      event.preventDefault();
	      addPriceBracket();
	    });
	    addPriceBracket();

      updatePricingFields();
      propagateUnitName();
      $(schemeSelect()).change(function(){
        updatePricingFields();
        propagateUnitName();
      });
      
      $('#quantity_based_component_unit_name').change(function() {
        propagateUnitName();
      });
      
      $('#metered_component_unit_name').change(function() {
        propagateUnitName();
      });
      
	  }
	}

})(jQuery);

$(document).ready(function() {

  Chargify.GatewaySettings.init();
  $('a[rel*=facebox]').facebox();

  // Open rel="external" links in a new tab/window
  $('a[rel*=_blank]').attr('target', '_blank');

  // Open operations panel
  $('a[href=#show-operations]').click(function(e){
    $(this).closest('tr').next('tr.operations').find('td').toggle();
    e.preventDefault();
  })

  // Open operations panel
  $('a.showOps').click(function(e){
    $(this).closest('tr').next('tr.operations').find('td').toggle();
    e.preventDefault();
  })
  
  $('.hoverable').hover(
    function() {
      $(this).addClass('hover');
    },
    function() {
      $(this).removeClass('hover');
    }
  );
  
  Chargify.MultiBracketPricingComponents.init();
  
  initAssociatedCheckboxes();

  $('a.dismissal').click(function(){
    var id = $(this).attr('rel');
    $.ajax({ url: "/notices/" + id + '/dismissals', type:'POST', success: function(){
      $("#notice-" + id).fade();
    }});
    return false;
  });

  $('input#enable_statement_emails_on_close').click(function(){
    if ($(this).is(':checked')) {
      $('#enable_statement_emails_on_later_payment').enable();
    }else{
      $('#enable_statement_emails_on_later_payment').attr('checked', false).disable();
    }
  });
  
  $('#new_usage').submit(function(){
    $('#record_usage_button').attr("disabled", "true");
    $('#record_usage_button').val("Recording...");
    return true;
  });
  $('#new_allocation').submit(function(){
    $('#record_allocation_button').attr("disabled", "true");
    $('#record_allocation_button').val("Updating...");
    return true;
  });
  
});

var initCountrySelect = function() {
  $('.country_select').change(function(){
    var code = $(this).val();
    var rel = $(this).attr('rel');
    $('#' + rel).html('<option>Please select</option>');
    $.getJSON('/subdivisions.json?code=' + code, function(data) {
      jQuery.each(data, function(i, row) {
        $('#' + rel).
             append($("<option></option>").
             attr("value",row.subdivision.code).
             text(row.subdivision.label));
          });
    });
  });  
}


var initAssociatedCheckboxes = function(){
  $('.has-associated-checkbox').click(function(){
    var isChecked = $(this).is(':checked');
    if(isChecked){
     var rel = $(this).attr('rel');
     $('#' + rel).attr('checked', true); 
    }
  }); 
}

$(document).ready(function() {
    $('input.ui-datepicker').datepicker();
});
