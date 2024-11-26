/*!
 *
 * AJAX API
 *
 * Author: Steffen Kroggel <developer@steffenkroggel.de>
 * Last updated: 10.11.2021
 */

(function ($, window, document, undefined) {
  // Create the defaults once
  // Additional options, extending the defaults, can be passed as an object from the initializing call
  var pluginName = 'ajaxApi',
    defaults = {
      // form: "form.ajax"
      boxSuccessClass: 'success',
      boxHintClass: 'hint',
      boxErrorClass: 'error',
      loadingIndicatorActiveClass: 'is-ajax-loading',
      loadingIndicatorTargetClass: 'is-ajax-target',
      loadingIndicatorHtml: '<div class="loading-indicator"></div>',
      loadingIndicatorHtmlClass: 'ajax-overlay',
    };

  // The plugin constructor
  function Plugin (element, options) {
    this.element = element;
    // Merge defaults with passed options
    this.settings = $.extend({}, defaults, options);

    this._defaults = defaults;
    this._name = pluginName;

    this.init();
  }

  $.extend(Plugin.prototype, {
    init: function () {
      // Setup elements and global variables
      this.settings.$el = $(this.element);
      this.settings.elementType = this.settings.$el.prop('tagName');

      if (this.settings.elementType === 'FORM') {
        this.settings.formElements = this.settings.$el.find(':input:not(.btn)');
        this.settings.url = this.settings.$el.attr('action');

        if (this.settings.$el.hasClass('ajax-feedback')) {
          this.settings.feedbackUrl = this.settings.$el.attr(
            'data-feedback-url'
          );
        }

        if (this.settings.$el.data('ajax-indicator-id')) {
          this.settings.indicatorTarget = $('#' + this.settings.$el.data('ajax-indicator-id'));
        } else {
          this.settings.indicatorTarget = this.settings.$el;
        }

      } else if (this.settings.elementType === 'A') {
        this.settings.url = this.settings.$el.attr('href');

        if (this.settings.$el.data('ajax-indicator-id')) {
          this.settings.indicatorTarget = $('#' + this.settings.$el.data('ajax-indicator-id'));
        }

      } else {
        if (this.settings.elementType === 'TEMPLATE') {
          this.settings.url = this.settings.$el.data('ajax-url');
          this.settings.ignore = false;

          if (
            (this.settings.$el.data('ajax-ignore'))
            && (
              (this.settings.$el.data('ajax-ignore') === 1)
              || (this.settings.$el.data('ajax-ignore').toLowerCase() === 'true')
            )
          ){
            this.settings.ignore = true;
          }

          if (! this.settings.ignore) {
            if (this.settings.$el.data('ajax-max-width')) {
              this.sendOnViewport();
            } else {
              this.sendOnPageLoad();
            }
          }
        }
      }
      this.bindEvents();
    },

    // Bind all event listeners for this plugin
    bindEvents: function () {

      if (this.settings.elementType === 'FORM') {
        var self = this;
        if (
          this.settings.$el.hasClass('ajax')
          && this.settings.$el.hasClass('ajax-feedback')
        ) {
          // AJAX Feedback form
          this.settings.formElements.each(function () {
            $(this)
              .on('change', self.sendField.bind(self));
          });
          this.settings.$el.on('submit', this.sendForm.bind(this));

        } else {
          if (this.settings.$el.hasClass('ajax')) {

            // Regular AJAX form
            this.settings.$el.on('submit', this.sendForm.bind(this));

            if (! this.settings.$el.hasClass('ajax-submit-only')) {
              this.settings.formElements.each(function () {
                $(this)
                  .on('change', self.sendForm.bind(self));
              });
            }

            if (this.settings.$el.find('.ajax-send')) {
              // AJAX send links
              this.settings.$el.find('.ajax-send')
                .each(function () {
                  $(this)
                    .on('click', self.sendFormByLink.bind(self));
                });
            }

            if (this.settings.$el.find('.ajax-override-submit')) {
              // AJAX target override form submit
              this.settings.$el.find('.ajax-override-submit')
                .each(function () {
                  $(this)
                    .on('click', self.sendNormalFormByLink.bind(self));
                });
            }

            if (this.settings.$el.find('.ajax-override')) {
              // AJAX override
              this.settings.$el.find('.ajax-override')
                .each(function () {
                  if ($(this)
                        .prop('tagName') === 'A') {
                    $(this)
                      .on('click', self.overrideForm.bind(self));
                  } else {
                    if ($(this)
                          .prop('tagName') === 'SELECT') {
                      $(this)
                        .on('change', self.overrideForm.bind(self));
                    }
                  }
                });
            }
          }
        }
      } else if (this.settings.elementType === 'A') {
        this.settings.$el.on('click', this.sendLink.bind(this));

      } else {
        if (this.settings.elementType === 'TEMPLATE') {
          if (this.settings.$el.data('ajax-max-width')) {
            jQuery(window).on('resize', this.sendOnViewport.bind(this));
          }
        }
      }
    },

    addLoadingIndicator: function (e) {

      var html = $.parseHTML('<div class="' + this.settings.loadingIndicatorHtmlClass + '">' + this.settings.loadingIndicatorHtml + '</div>');
      this.settings.$el.addClass(this.settings.loadingIndicatorActiveClass).blur();

      if (this.settings.indicatorTarget) {
        this.settings.indicatorTarget.addClass(this.settings.loadingIndicatorTargetClass);
        this.settings.indicatorTarget.append(html);
      }
    },


    removeLoadingIndicator: function () {

      try {
        this.settings.$el.removeClass(this.settings.loadingIndicatorActiveClass).blur();
      } catch (e) {
        // do nothing - element may not exist any more
      }

      if (this.settings.indicatorTarget) {
        this.settings.indicatorTarget.removeClass(this.settings.loadingIndicatorTargetClass);
        this.settings.indicatorTarget.find('.' + this.settings.loadingIndicatorHtmlClass).remove();
      }
    },

    sendForm: function (e) {
      e.preventDefault();

      if (!this.settings.$el.hasClass('override-submit')) {
        var url = this.settings.url;
        var data = this.getFormValues();
        var requestId = this.generateRequestId();

        this.ajaxRequest(requestId, url, data);
        this.addLoadingIndicator(e);

        if (this.settings.$el.hasClass('ajax-scroll-top')) {
          $('html, body')
            .stop()
            .animate(
              {
                scrollTop: this.settings.$el.offset().top,
              },
              1000,
              "easeOutQuart"
            );
        }
      }
    },

    sendFormByLink: function (e) {
      // Convenience function – just calls sendForm :)
      e.preventDefault();
      this.sendForm(e);
    },

    sendNormalFormByLink: function (e) {
      e.preventDefault();
      var link = $(e.currentTarget);
      var form = link.closest('form.ajax');

      // Override form action
      form.attr('action', link.attr('href'))
        .addClass('override-submit');
      // Unbind AJAX formSubmit event listener
      form.unbind('submit');
      // Submit form regularly
      form.submit();
    },

    sendLink: function (e) {
      e.preventDefault();

      var url = this.settings.url;
      var data = [];
      var requestId = this.generateRequestId();

      if (!this.settings.$el.hasClass(this.settings.loadingIndicatorActiveClass)) {
        this.ajaxRequest(requestId, url, data);
        this.addLoadingIndicator(e);
      }
    },

    sendField: function (e) {
      var field = $(e.currentTarget);
      var url = this.settings.feedbackUrl;
      var data = field.serializeArray();
      var requestId = this.generateRequestId();

      this.ajaxRequest(requestId, url, data);
    },

    overrideForm: function (e) {
      e.preventDefault();
      var target = $(e.currentTarget);
      var form = this.settings.$el;
      var data = form
        .find('select, input')
        .not('.ajax-override')
        .serializeArray();
      var requestId = this.generateRequestId();
      var url;

      if (target.prop('tagName') === 'A') {
        url = target.attr('href');
      } else {
        if (target.prop('tagName') === 'SELECT') {
          url = target.val();
        }
      }

      if (url) {
        this.ajaxRequest(requestId, url, data);
      }
    },

    sendOnPageLoad: function () {
      var url = this.settings.url;
      var data = [];
      var requestId = this.generateRequestId();

      if (url) {
        this.ajaxRequest(requestId, url, data, true);
      }
    },

    sendOnViewport: function () {
      var url = this.settings.url;
      var templateTag = this.settings.$el;
      var data = [];
      var requestId = this.generateRequestId();

      if (
        (url)
        && (templateTag.data('ajax-max-width') >= jQuery(window).width())
      ){
        this.ajaxRequest(requestId, url, data, true);
      }
    },

    getFormValues: function () {
      return this.settings.$el.serializeArray();
    },

    generateRequestId: function() {
      return Math.random().toString(36).substring(5);
    },

    ajaxRequest: function (requestId, url, data, background) {
      var self = this;

      // Add typeNum and requestId to data array
      // Do not set requestId so we can use Varnish on Ajax-Requests if wanted
      // data.unshift({name: 'rid', value: requestId});
      data.unshift({name: 'type', value: 250});

      $.ajax({
               method: 'post',
               url: url,
               data: $.param(data),
               dataType: 'json',
               complete: function (response) {
                 try {

                   // Successful request
                   response = JSON.parse(response.responseText);
                   // console.log(response);
                   self.parseContent(response);
                   self.removeLoadingIndicator();
                   if (! background) {
                     self.updateBrowserHistory(response, url, $.param(data));
                   }
                 } catch (error) {
                   // Error in request
                   console.log(error.message);
                 }
               }
             });
    },
    parseContent: function (json) {
      for (var property in json) {
        if (property === 'message') {
          var messageObject = json[property];
          for (var parent in messageObject) {
            var messageContent = this.getMessageBox(
              messageObject[parent].message,
              messageObject[parent].type,
              parent
            );
            this.appendContent(parent, messageContent);
          }
        } else {
          if (property === 'data') {
            // TBD – more input needed
          } else {
            if (property === 'html') {
              var htmlObject = json[property];
              for (var parent in htmlObject) {
                for (var method in htmlObject[parent]) {
                  if (method === 'append') {
                    this.appendContent(parent, htmlObject[parent][method]);
                  } else {
                    if (method === 'prepend') {
                      this.prependContent(parent, htmlObject[parent][method]);
                    } else {
                      if (method === 'replace') {
                        this.replaceContent(parent, htmlObject[parent][method]);
                      }
                    }
                  }
                }
              }
            }
          }
        }
      }
    },

    appendContent: function (element, content) {
      try {
        var oldContent = jQuery('#' + element);
        oldContent.find('a').last().focus();

        var newContent = jQuery(content);
        newContent.ajaxApi().find('.ajax').ajaxApi();
        newContent.appendTo(oldContent);

        jQuery(document)
          .trigger(
            'tx-ajax-api-content-changed',
            newContent.parent()
          );
      } catch (error) {
        console.log(error.message);
      }
    },

    prependContent: function (element, content) {
      try {
        var newContent = jQuery(content);
        newContent.ajaxApi().find('.ajax').ajaxApi(); // we call it twice: for with an without wrapping tags
        newContent.prependTo(jQuery('#' + element));

        jQuery(document)
          .trigger(
            'tx-ajax-api-content-changed',
            newContent.parent()
          );
      } catch (error) {
        console.log(error.message);
      }
    },

    replaceContent: function (element, content) {
      try {
        if (jQuery(content).length > 0) {
          var newContent = jQuery(content);
          newContent.ajaxApi().find('.ajax').ajaxApi(); // we call it twice: for with an without wrapping tags
          newContent.appendTo(
            jQuery('#' + element)
              .empty()
          );

          jQuery(document)
            .trigger('tx-ajax-api-content-changed',
                     newContent
            );

        } else {
          jQuery('#' + element)
            .empty()
            .append(content);
        }
      } catch (error) {
        console.log(error.message);
      }
    },

    getMessageBox: function (text, type, parent) {
      var box = jQuery(
        '<div class="message-box" data-for="#' + parent + '">' + text + '</div>'
      );
      if (type === 1) {
        box.addClass(this.settings.boxSuccessClass);
      } else {
        if (type === 2) {
          box.addClass(this.settings.boxHintClass);
        } else {
          if (type === 99) {
            box.addClass(this.settings.boxErrorClass);
          }
        }
      }

      return box;
    },

    updateBrowserHistory: function (response, url, data) {
      /*var finalUrl = url;
      if (data) {
        if (finalUrl.indexOf('?')) {
          finalUrl += '&' + data;
        } else {
          finalUrl += '?' + data;
        }
      }
      history.pushState(
        response,
        document.title,
        window.location.pathname
      );*/
    },
  });

  // A really lightweight plugin wrapper around the constructor,
  // preventing against multiple instantiations
  $.fn[pluginName] = function (options) {
    return this.each(function () {
      if (!$.data(this, 'plugin_' + pluginName)) {
        $.data(this, 'plugin_' + pluginName, new Plugin(this, options));
      }
    });
  };
})(jQuery, window, document);
