/**
 *
 * Author: Ivan S. Acog
 */

(function($) {
    /**
     * Dialog
     */
    var Dialog = function(element, options) {
        this.options = options;
        this.element = $(element);

        this.element.on('click.base.dialog.close', '[data-close="dialog"]', $.proxy(this.close, this));
    };

    Dialog.prototype.open = function() {
        // Wrap dialog into modal
        this.element.wrap('<div class="sc-modal-ui" />');
        this.element.addClass('open');

        $(document).trigger('base.api.dialog.opened');

        if (this.options != undefined) {
            if (this.options.onopen != undefined) {
                this.options.onopen();
            }
        }
    };

    Dialog.prototype.close = function() {
        this.element.removeClass('open')
                    .unwrap();

        $(document).trigger('base.api.dialog.closed');
    };

    $.fn.dialog = function(options) {
        $(document).on('click.base.api.dialog.toggle', '[data-toggle="dialog"]', function() {
            var toggle = $(this);
            var target = toggle.data('target');

            if (target !== undefined) {
                var modal = $('[data-target="' + target + '"].sc-dialog-ui').data('base.dialog');
                if (modal) {
                    modal.open();
                }
            }
        });

        $(document)
            .on('base.api.dialog.opened', function() { $(document.body).addClass('sc-dialog-open'); })
            .on('base.api.dialog.closed', function() { $(document.body).removeClass('sc-dialog-open'); });


        return this.each(function() {
            var element = $(this);
            var data    = element.data('base.dialog');

            // Check whether the dialog has been initialized
            if (!data) element.data('base.dialog', (data = new Dialog(this, options)));
        });
    }
	
	
	
})(jQuery);

function scInquirySend(sender) {
    /** 
     * Send the inquiry @ http://sailchecker.com/sandbox/yacht-finder/inquiry/
     *
     * Get field values...
     */

    var name    = jQuery('#inquiryInputName').val();
    var email   = jQuery('#inquiryInputEmail').val();
    var phone   = jQuery('#inquiryInputPhone').val();
    var message = jQuery('#inquiryInputMessage').val();

    // Email field is required...
    if (email !== '') {
        // Check if we have a valid email of the user...
        var validEmailRegEx = /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i;
        if (validEmailRegEx.test(email)) {
            jQuery(sender).html("Sending...");

            // Email address is valid
            var request = jQuery.ajax({
                type: 'POST',
                data: {
                    name:       name,
                    email:      email,
                    phone:      phone,
                    message:    message
                },
                dataType: 'json'
            });

            request.always(function() {
                jQuery(sender).html("Send");
                jQuery('#sc-dialog-ui-inquire-message').hide();
                jQuery('#sc-dialog-ui-inquire-message').removeClass("sc-dialog-ui-inquire-message-error");
                jQuery('#sc-dialog-ui-inquire-message').removeClass("sc-dialog-ui-inquire-message-success");
            });

            request.done(function(data) {
                jQuery('#sc-dialog-ui-inquire-message').html(data.message);
                if (data.status == 'ok') {
                    jQuery('#sc-dialog-ui-inquire-message').addClass("sc-dialog-ui-inquire-message-success");
                    jQuery('#sc-dialog-ui-inquire-message').fadeIn();
                } else {
                    jQuery('#sc-dialog-ui-inquire-message').addClass("sc-dialog-ui-inquire-message-error");
                    jQuery('#sc-dialog-ui-inquire-message').fadeIn();
                }
            });

            request.fail(function(jqXHR, status, errorThrown) {
                jQuery('#sc-dialog-ui-inquire-message').addClass("sc-dialog-ui-inquire-message-error");
                jQuery('#sc-dialog-ui-inquire-message').html("Oops! Please try again.");
                jQuery('#sc-dialog-ui-inquire-message').fadeIn();

                for (var x in jqXHR) {
                    console.log(x + ": " + jqXHR[x]);
                }
            });
        } else {
            jQuery('#sc-dialog-ui-inquire-message').addClass("sc-dialog-ui-inquire-message-error");
            jQuery('#sc-dialog-ui-inquire-message').html("Email address you entered is invalid.");
            jQuery('#sc-dialog-ui-inquire-message').fadeIn();
        }
    } else {
        jQuery('#sc-dialog-ui-inquire-message').addClass("sc-dialog-ui-inquire-message-error");
        jQuery('#sc-dialog-ui-inquire-message').html("Please enter your email address.");
        jQuery('#sc-dialog-ui-inquire-message').fadeIn();
    }
}

function verifyAweberSubscribeForm(form, event) {
    var name    = form.name.value;
    var email   = form.email.value;

    // Check if name and email are not empty
    if (name !== '' && email !== '') {
        // Check if the email is valid
        var validEmailRegEx = /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i;
        if (validEmailRegEx.test(email)) {
            // Ok, go...
        } else {
            jQuery(form.querySelector('#sc-aweber-ui-error')).html("Please enter a valid e-mail address.");
            jQuery(form.querySelector('#sc-aweber-ui-error')).fadeIn();

            event.preventDefault();
            return false;
        }
    } else {
        jQuery(form.querySelector('#sc-aweber-ui-error')).html("Please enter your name and email.");
        jQuery(form.querySelector('#sc-aweber-ui-error')).fadeIn();

        event.preventDefault();
        return false;
    }
}

    // Jquery Animation TextArea

(function ($) {
  // writes the string
  //
  // @param jQuery $target
  // @param String str
  // @param Numeric cursor
  // @param Numeric delay
  // @param Function cb
  // @return void
  function typeString($target, str, cursor, delay, cb) {
    $target.html(function (_, html) {
      return html + str[cursor];
    });
    
    if (cursor < str.length - 1) {
      setTimeout(function () {
        typeString($target, str, cursor + 1, delay, cb);
      }, delay);
    }
    else {
      cb();
    }
  }
  
  // clears the string
  //
  // @param jQuery $target
  // @param Numeric delay
  // @param Function cb
  // @return void
  function deleteString($target, delay, cb) {
    var length;
   // return $target.html('');
    $target.html(function (_, html) {
      length = html.length;
      return html.substr(0, length - 1);
    });
    
    if (length > 1) {
      setTimeout(function () {
        deleteString($target, delay, cb);
      }, delay);
    }
    else {
      cb();
    }
  }

  // jQuery hook
  $.fn.extend({
    teletype: function (opts) {
      var settings = $.extend({}, $.teletype.defaults, opts);
      
      return $(this).each(function () {
        (function loop($tar, idx) {
          // type
          typeString($tar, settings.text[idx], 0, settings.delay, function () {
            // delete
            setTimeout(function () {
              deleteString($tar, settings.delay, function () {
                loop($tar, (idx + 1) % settings.text.length);
              });
            }, settings.pause);
          });
        
        }($(this), 0));
      });
    }
  });

  // plugin defaults  
  $.extend({
    teletype: {
      defaults: {
        delay: 100,
        pause: 5000,
        text: []
      }
    }
  });
}(jQuery));

$('.typewriter').teletype({
  text: [
    'test2',
  ]
});

$(document).ready(function(e) {
$('.typewriter').click(function(e) {
    $(this).hide();
	$('.typewriter').prev('textarea').css('color','#000');
});

 
$('.typewriter').prev('textarea').focusout(function(e){
$('.typewriter').show();	
$(this).css('color','transparent');
});
});

