/*!
 * @visao/viewer-api v0.0.1-beta.6
 * (c) Visao
 * Released under the MIT License.
 */

(function (global, factory) {
  typeof exports === "object" && typeof module !== "undefined"
    ? factory(exports)
    : typeof define === "function" && define.amd
    ? define(["exports"], factory)
    : ((global =
        typeof globalThis !== "undefined" ? globalThis : global || self),
      factory((global.VisaoAPI = {})));
})(this, function (exports) {
  var _a;
  /**
   * Represents the state of the viewer. Certain methods will need to have a or multiple specific status on order
   * to be executed.
   *
   * > `unmounted` -> viewer's HTML element is yet to be mounted in the DOM.
   *
   * > `mounted` -> viewer's HTML element has been mounted in the DOM.
   *
   * > `loading` -> a 3D model is currently loading into the viewer.
   *
   * > `loaded` -> a 3D is loaded in the viewer and is ready to be manipulated.
   */
  exports.ViewerStatus = void 0;
  (function (ViewerStatus) {
    ViewerStatus["UNMOUNTED"] = "unmounted";
    ViewerStatus["MOUNTED"] = "mounted";
    ViewerStatus["LOADING"] = "loading";
    ViewerStatus["LOADED"] = "loaded";
  })(exports.ViewerStatus || (exports.ViewerStatus = {}));
  /**
   * @ignore
   */
  var statusLevels =
    ((_a = {}),
    (_a[exports.ViewerStatus.UNMOUNTED] = 1),
    (_a[exports.ViewerStatus.MOUNTED] = 2),
    (_a[exports.ViewerStatus.LOADING] = 3),
    (_a[exports.ViewerStatus.LOADED] = 4),
    _a);
  /**
   * @ignore
   */
  exports.ViewerAPIMessage = void 0;
  (function (ViewerAPIMessage) {
    // GETTERS - Request the viewer
    ViewerAPIMessage["GET_VIEWER"] = "VISAO/GET_VIEWER";
    ViewerAPIMessage["GET_STATUS"] = "VISAO/GET_STATUS";
    ViewerAPIMessage["GET_VARIANT_INFO"] = "VISAO/GET_VARIANT_INFO";
    ViewerAPIMessage["GET_LANGUAGE_INFO"] = "VISAO/GET_LANGUAGE_INFO";
    ViewerAPIMessage["GET_DEMOS"] = "VISAO/GET_DEMOS";
    // SENDERS - Send back to the client
    ViewerAPIMessage["SEND_VIEWER"] = "VISAO/SEND_VIEWER";
    ViewerAPIMessage["SEND_STATUS"] = "VISAO/SEND_STATUS";
    ViewerAPIMessage["SEND_VARIANT_INFO"] = "VISAO/SEND_VARIANT_INFO";
    ViewerAPIMessage["SEND_LANGUAGE_INFO"] = "VISAO/SEND_LANGUAGE_INFO";
    ViewerAPIMessage["SEND_DEMOS"] = "VISAO/SEND_DEMOS";
    // ACTIONS - Execute in the viewer
    ViewerAPIMessage["UPDATE_VARIANT"] = "VISAO/UPDATE_VARIANT";
    ViewerAPIMessage["UPDATE_LANGUAGE"] = "VISAO/UPDATE_LANGUAGE";
    ViewerAPIMessage["START_AR"] = "VISAO/START_AR";
    ViewerAPIMessage["SHOW_STEP"] = "VISAO/SHOW_STEP";
    ViewerAPIMessage["NEXT_STEP"] = "VISAO/NEXT_STEP";
    ViewerAPIMessage["PREVIOUS_STEP"] = "VISAO/PREVIOUS_STEP";
    ViewerAPIMessage["CLOSE_STEP"] = "VISAO/CLOSE_STEP";
    ViewerAPIMessage["PLAY"] = "VISAO/PLAY";
    ViewerAPIMessage["PAUSE"] = "VISAO/PAUSE";
    ViewerAPIMessage["RESET_CAMERA"] = "VISAO/RESET_CAMERA";
    ViewerAPIMessage["LOCK_CAMERA"] = "VISAO/LOCK_CAMERA";
    ViewerAPIMessage["UNLOCK_CAMERA"] = "VISAO/UNLOCK_CAMERA";
    ViewerAPIMessage["SHOW_HELP"] = "VISAO/SHOW_HELP";
    ViewerAPIMessage["CLOSE_HELP"] = "VISAO/CLOSE_HELP";
    ViewerAPIMessage["SHOW_QR"] = "VISAO/SHOW_QR";
    ViewerAPIMessage["CLOSE_QR"] = "VISAO/CLOSE_QR";
  })(exports.ViewerAPIMessage || (exports.ViewerAPIMessage = {}));
  /**
   * @ignore
   */
  exports.ViewerAPIMessageSource = void 0;
  (function (ViewerAPIMessageSource) {
    ViewerAPIMessageSource["VIEWER"] = "visao_viewer";
    ViewerAPIMessageSource["API"] = "visao_viewer_api";
  })(exports.ViewerAPIMessageSource || (exports.ViewerAPIMessageSource = {}));

  /**
   * @ignore
   */
  var listenToMessages = function (incomingMessageHandler) {
    window.addEventListener("message", incomingMessageHandler);
  };
  /**
   * @ignore
   */
  var unListenToMessages = function (incomingMessageHandler) {
    window.removeEventListener("message", incomingMessageHandler);
  };
  /**
   * @ignore
   */
  var parseIncomingMessage = function (event, source) {
    try {
      if (!event.data || typeof event.data !== "string") {
        return undefined;
      }
      var message = JSON.parse(event.data);
      if (
        (message === null || message === void 0 ? void 0 : message.source) !==
        source
      ) {
        return undefined;
      }
      return message;
    } catch (error) {
      console.error(
        source + " - Could not parse the latest message",
        event,
        error
      );
      return undefined;
    }
  };

  var LocalizedContent = /** @class */ (function () {
    function LocalizedContent(props) {
      if (props === void 0) {
        props = {};
      }
      this.language = "en";
      this.content = "";
      Object.assign(this, props);
    }
    return LocalizedContent;
  })();

  var Step = /** @class */ (function () {
    function Step(props) {
      if (props === void 0) {
        props = {};
      }
      this.id = "";
      this.name = "";
      this.position = 0;
      this.duration = 0;
      this.localizations = [];
      Object.assign(this, props);
    }
    return Step;
  })();

  var Demo = /** @class */ (function () {
    function Demo(props) {
      if (props === void 0) {
        props = {};
      }
      this.id = "";
      this.name = "";
      this.steps = [];
      this.localizations = [];
      Object.assign(this, props);
    }
    return Demo;
  })();

  /******************************************************************************
  Copyright (c) Microsoft Corporation.

  Permission to use, copy, modify, and/or distribute this software for any
  purpose with or without fee is hereby granted.

  THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES WITH
  REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF MERCHANTABILITY
  AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY SPECIAL, DIRECT,
  INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES WHATSOEVER RESULTING FROM
  LOSS OF USE, DATA OR PROFITS, WHETHER IN AN ACTION OF CONTRACT, NEGLIGENCE OR
  OTHER TORTIOUS ACTION, ARISING OUT OF OR IN CONNECTION WITH THE USE OR
  PERFORMANCE OF THIS SOFTWARE.
  ***************************************************************************** */

  var __assign = function () {
    __assign =
      Object.assign ||
      function __assign(t) {
        for (var s, i = 1, n = arguments.length; i < n; i++) {
          s = arguments[i];
          for (var p in s)
            if (Object.prototype.hasOwnProperty.call(s, p)) t[p] = s[p];
        }
        return t;
      };
    return __assign.apply(this, arguments);
  };

  function __spreadArray(to, from, pack) {
    if (pack || arguments.length === 2)
      for (var i = 0, l = from.length, ar; i < l; i++) {
        if (ar || !(i in from)) {
          if (!ar) ar = Array.prototype.slice.call(from, 0, i);
          ar[i] = from[i];
        }
      }
    return to.concat(ar || Array.prototype.slice.call(from));
  }

  function logErrorBlock(messageBlock) {
    if (messageBlock === void 0) {
      messageBlock = [];
    }
    var header = "VISAO > An error occurred while using the viewer API:";
    console.error(__spreadArray([header], messageBlock, true).join("\n\t\t"));
  }

  /**
   * @ignore
   */
  var Callbacks = /** @class */ (function () {
    function Callbacks(store) {
      this.store = store;
    }
    Callbacks.prototype.getFrom = function (key) {
      var _a;
      return (_a = this.store[key]) !== null && _a !== void 0 ? _a : [];
    };
    Callbacks.prototype.addTo = function (key, callbackFn) {
      var callback = { id: Date.now().toString(), fn: callbackFn };
      this.store[key] = __spreadArray(
        __spreadArray([], this.getFrom(key), true),
        [callback],
        false
      );
      return callback.id;
    };
    Callbacks.prototype.removeByCallbackFunctionFrom = function (
      key,
      callbackFn
    ) {
      this.store[key] = this.getFrom(key).filter(function (callback) {
        return callback.fn !== callbackFn;
      });
    };
    Callbacks.prototype.removeByCallbackIdFrom = function (key, callbackId) {
      if (!callbackId) {
        return;
      }
      this.store[key] = this.getFrom(key).filter(function (callback) {
        return callback.id !== callbackId;
      });
    };
    Callbacks.prototype.executeFor = function (key, payload) {
      this.getFrom(key).forEach(function (callback) {
        callback.fn(payload);
      });
    };
    return Callbacks;
  })();

  /**
   * The Visao class is the starting point of the Visao viewer api.
   * It encapsulates all the necessary calls to interact with your viewers from your
   * own code base.
   */
  var Visao = /** @class */ (function () {
    /**
     * #### Description
     * The constructor requires an element id given to the iframe tag/component
     * defined in your application. Internally, we take care of finding the element
     * in the DOM once the component is mounted.
     *
     * #### Usage
     *
     * ```typescript
     * const instance = new Visao("viewer-iframe-id");
     * ```
     *
     * @param id
     * The id of the viewer element.
     */
    function Visao(id) {
      var _a;
      var _this = this;
      this.viewerElement = null;
      this.status = exports.ViewerStatus.UNMOUNTED;
      this.handleIncomingMessage = function (event) {
        var message = parseIncomingMessage(
          event,
          exports.ViewerAPIMessageSource.VIEWER
        );
        if (!message) {
          return;
        }
        switch (message.type) {
          case exports.ViewerAPIMessage.SEND_STATUS: {
            var status_1 = message.payload.status;
            _this.status = status_1;
            if (status_1 === exports.ViewerStatus.MOUNTED) {
              _this.setViewerElement(document.getElementById(_this.id));
            }
            _this.callbacks.executeFor(
              exports.ViewerAPIMessage.GET_STATUS,
              status_1
            );
            break;
          }
          case exports.ViewerAPIMessage.SEND_LANGUAGE_INFO: {
            var languageInformation = message.payload;
            _this.callbacks.executeFor(
              exports.ViewerAPIMessage.GET_LANGUAGE_INFO,
              languageInformation
            );
            _this.callbacks.removeByCallbackIdFrom(
              exports.ViewerAPIMessage.GET_LANGUAGE_INFO,
              message.callbackId
            );
            break;
          }
          case exports.ViewerAPIMessage.SEND_VARIANT_INFO: {
            var variantInformation = message.payload;
            _this.callbacks.executeFor(
              exports.ViewerAPIMessage.GET_VARIANT_INFO,
              variantInformation
            );
            _this.callbacks.removeByCallbackIdFrom(
              exports.ViewerAPIMessage.GET_VARIANT_INFO,
              message.callbackId
            );
            break;
          }
          case exports.ViewerAPIMessage.SEND_DEMOS: {
            var demos = message.payload;
            _this.callbacks.executeFor(
              exports.ViewerAPIMessage.GET_DEMOS,
              demos
            );
            _this.callbacks.removeByCallbackIdFrom(
              exports.ViewerAPIMessage.GET_DEMOS,
              message.callbackId
            );
            break;
          }
        }
      };
      this.id = id;
      this.callbacks = new Callbacks(
        ((_a = {}),
        (_a[exports.ViewerAPIMessage.GET_STATUS] = []),
        (_a[exports.ViewerAPIMessage.GET_LANGUAGE_INFO] = []),
        (_a[exports.ViewerAPIMessage.GET_VARIANT_INFO] = []),
        (_a[exports.ViewerAPIMessage.GET_DEMOS] = []),
        _a)
      );
      listenToMessages(this.handleIncomingMessage);
    }
    /**
     * #### Description
     * > Manually set the reference of the viewer from the id.
     *
     * #### Usage:
     *
     * ```typescript
     * visao.setViewerElementFromId('viewer-id');
     * ```
     *
     *  @param id
     *  HTML element id representing the viewer
     */
    Visao.prototype.setViewerElementFromId = function (id) {
      this.id = id;
      this.viewerElement = document.getElementById(id);
    };
    /**
     * #### Description
     * > Manually set the reference of the viewer element.
     *
     * #### Usage:
     *
     * ```typescript
     * const element = document.getElementById('viewer-id');
     * visao.setViewerElement(element);
     * ```
     *
     *  @param viewerElement
     *  HTML element representing the viewer
     */
    Visao.prototype.setViewerElement = function (viewerElement) {
      this.viewerElement = viewerElement;
    };
    /**
     * #### Description
     * > Listens to the state of the viewer. It will notify by calling
     * the provided parameter callback providing the new state everytime
     * the state changes.
     *
     * #### Usage
     *
     * ```typescript
     * visao.listenToViewerStatus((newViewerStatus: ViewerStatus) => {
     *   // Do Something
     * });
     * ```
     *
     * @param callback
     * Callback to execute when a change happen
     */
    Visao.prototype.listenToViewerStatus = function (callback) {
      this.callbacks.addTo(exports.ViewerAPIMessage.GET_STATUS, callback);
    };
    /**
     * #### Description
     * > Removes a viewer status listener.
     *
     * #### Usage
     *
     * ```typescript
     * visao.unListenToViewerStatus(callback);
     * ```
     *
     * @param callback
     * Callback which matches the one provided to a previously called {@link listenToViewerStatus}
     */
    Visao.prototype.unListenToViewerStatus = function (callback) {
      this.callbacks.removeByCallbackFunctionFrom(
        exports.ViewerAPIMessage.GET_STATUS,
        callback
      );
    };
    /**
     * #### Description
     * > Listens to any change to variants in the viewer. It will notify by calling
     * the provided parameter callback providing the new state everytime
     * the state changes.
     *
     * #### Usage
     *
     * ```typescript
     * visao.listenToVariantInfoChange((variantInformation: VariantInformationPayload) => {
     *   // Do Something
     * });
     * ```
     *
     * @param callback
     * Callback to execute when a change happen
     */
    Visao.prototype.listenToVariantInfoChange = function (callback) {
      this.callbacks.addTo(exports.ViewerAPIMessage.GET_VARIANT_INFO, callback);
    };
    /**
     * #### Description
     * > Removes a variant info change listener.
     *
     * #### Usage
     *
     * ```typescript
     * visao.unListenToVariantInfoChange(callback);
     * ```
     *
     * @param callback
     * Callback which matches the one provided to a previously called {@link listenToVariantInfoChange}
     */
    Visao.prototype.unListenToVariantInfoChange = function (callback) {
      this.callbacks.removeByCallbackFunctionFrom(
        exports.ViewerAPIMessage.GET_VARIANT_INFO,
        callback
      );
    };
    /**
     * #### Description
     * > Listens to any change to the language in the viewer. It will notify by calling
     * the provided parameter callback providing the new state everytime
     * the state changes.
     *
     * #### Usage
     *
     * ```typescript
     * visao.listenToLanguageInfoChange((languageInformation: LanguageInformationPayload) => {
     *   // Do Something
     * });
     * ```
     *
     * @param callback
     * Callback to execute when a change happen
     */
    Visao.prototype.listenToLanguageInfoChange = function (callback) {
      this.callbacks.addTo(
        exports.ViewerAPIMessage.GET_LANGUAGE_INFO,
        callback
      );
    };
    /**
     * #### Description
     * > Removes a language info change listener.
     *
     * #### Usage
     *
     * ```typescript
     * visao.unListenToLanguageInfoChange(callback);
     * ```
     *
     * @param callback
     * Callback which matches the one provided to a previously called {@link listenToLanguageInfoChange}
     */
    Visao.prototype.unListenToLanguageInfoChange = function (callback) {
      this.callbacks.removeByCallbackFunctionFrom(
        exports.ViewerAPIMessage.GET_LANGUAGE_INFO,
        callback
      );
    };
    /**
     * #### Description
     * > Listens to any change to the demos in the viewer. It will notify by calling
     * the provided parameter callback providing the new state everytime
     * the state changes.
     *
     * #### Usage
     *
     * ```typescript
     * visao.listenToDemosChange((demos: DemosPayload) => {
     *   // Do Something
     * });
     * ```
     *
     * @param callback
     * Callback to execute when a change happen
     */
    Visao.prototype.listenToDemosChange = function (callback) {
      this.callbacks.addTo(exports.ViewerAPIMessage.GET_DEMOS, callback);
    };
    /**
     * #### Description
     * > Removes a demos change listener.
     *
     * #### Usage
     *
     * ```typescript
     * visao.unListenToDemosChange(callback);
     * ```
     *
     * @param callback
     * Callback which matches the one provided to a previously called {@link listenToDemosChange}
     */
    Visao.prototype.unListenToDemosChange = function (callback) {
      this.callbacks.removeByCallbackFunctionFrom(
        exports.ViewerAPIMessage.GET_DEMOS,
        callback
      );
    };
    /**
     * #### Description
     * >  Get the data for all the demos and their steps.
     *
     * #### Usage
     *  > :warning: **{@link ViewerStatus}**  must be at **{@link ViewerStatus.LOADED}** to execute.
     *
     * ```typescript
     * // Get the demos data
     * let demos;
     * visao.getDemos((payload) => {
     *   demos = payload.demos;
     *   // Do something with the data
     * });
     * ```
     * @param callback
     * Callback with a payload as parameter which includes the list of demos.
     */
    Visao.prototype.getDemos = function (callback) {
      var callbackId = this.callbacks.addTo(
        exports.ViewerAPIMessage.GET_DEMOS,
        callback
      );
      this.executeAction(
        {
          type: exports.ViewerAPIMessage.GET_DEMOS,
          callbackId: callbackId
        },
        exports.ViewerStatus.LOADED
      );
    };
    /**
     * #### Description
     * > Displays provided demo step.
     *
     * #### Usage
     *  > :warning: **{@link ViewerStatus}**  must be at **{@link ViewerStatus.LOADED}** to execute.
     *
     * ```typescript
     * const stepName = 'shoe laces';
     * visao.showStep(stepName);
     * ```
     *
     * @param step
     * The name of the step.
     */
    Visao.prototype.showStep = function (step) {
      this.executeAction(
        {
          type: exports.ViewerAPIMessage.SHOW_STEP,
          payload: {
            step: step
          }
        },
        exports.ViewerStatus.LOADED
      );
    };
    /**
     * #### Description
     * > Closes currently displayed demo step.
     *
     * #### Usage
     *  > :warning: **{@link ViewerStatus}**  must be at **{@link ViewerStatus.LOADED}** to execute.
     *
     * ```typescript
     * visao.closeStep();
     * ```
     */
    Visao.prototype.closeStep = function () {
      this.executeAction(
        {
          type: exports.ViewerAPIMessage.CLOSE_STEP
        },
        exports.ViewerStatus.LOADED
      );
    };
    /**
     * #### Description
     * > Displays the previous demo step.
     *
     * #### Usage
     *  > :warning: **{@link ViewerStatus}**  must be at **{@link ViewerStatus.LOADED}** to execute.
     *
     * ```typescript
     * visao.previousStep();
     * ```
     */
    Visao.prototype.previousStep = function () {
      this.executeAction(
        {
          type: exports.ViewerAPIMessage.PREVIOUS_STEP
        },
        exports.ViewerStatus.LOADED
      );
    };
    /**
     * #### Description
     * > Displays the next demo step.
     *
     * #### Usage
     *  > :warning: **{@link ViewerStatus}**  must be at **{@link ViewerStatus.LOADED}** to execute.
     *
     * ```typescript
     * visao.nextStep();
     * ```
     */
    Visao.prototype.nextStep = function () {
      this.executeAction(
        {
          type: exports.ViewerAPIMessage.NEXT_STEP
        },
        exports.ViewerStatus.LOADED
      );
    };
    /**
     * #### Description
     * > Starts the demo steps player.
     *
     * #### Usage
     * > :warning: **{@link ViewerStatus}**  must be at **{@link ViewerStatus.LOADED}** to execute.
     *
     * ```typescript
     * visao.play();
     * ```
     */
    Visao.prototype.play = function () {
      this.executeAction(
        {
          type: exports.ViewerAPIMessage.PLAY
        },
        exports.ViewerStatus.LOADED
      );
    };
    /**
     * #### Description
     * > Pauses the demo steps player.
     *
     * #### Usage
     * > :warning: **{@link ViewerStatus}**  must be at **{@link ViewerStatus.LOADED}** to execute.
     *
     * ```typescript
     * visao.pause();
     * ```
     */
    Visao.prototype.pause = function () {
      this.executeAction(
        {
          type: exports.ViewerAPIMessage.PAUSE
        },
        exports.ViewerStatus.LOADED
      );
    };
    /**
     * #### Description
     * > Gets the current language information
     *
     * #### Usage
     * > :warning: **{@link ViewerStatus}**  must be either at **{@link ViewerStatus.MOUNTED}**,
     *  **{@link ViewerStatus.LOADING}**, **{@link ViewerStatus.LOADED}** to execute.
     *
     * ```typescript
     * visao.getLanguageInformation((info) => {
     *   const currentLanguage = info.language;
     *   const availableLanguages = info.languages;
     * });
     * ```
     *
     *  @param callback
     *  The callback will contain the currently displayed languages as well as all the supported languages in the viewer.
     */
    Visao.prototype.getLanguageInformation = function (callback) {
      var callbackId = this.callbacks.addTo(
        exports.ViewerAPIMessage.GET_LANGUAGE_INFO,
        callback
      );
      this.executeAction(
        {
          type: exports.ViewerAPIMessage.GET_LANGUAGE_INFO,
          callbackId: callbackId
        },
        exports.ViewerStatus.MOUNTED
      );
    };
    /**
     * #### Description
     * > Tells the viewer to change the language of the different localized content if it can be found.
     * This will be visible on UI components like tooltips, menus, flyers, etc...
     *
     * #### Usage
     * > :warning: **{@link ViewerStatus}**  must be either at **{@link ViewerStatus.MOUNTED}**,
     *  **{@link ViewerStatus.LOADING}**, **{@link ViewerStatus.LOADED}** to execute.
     *
     * ```typescript
     * // Change the viewer language to french
     * visao.changeLanguage('fr');
     * ```
     *
     *  @param language
     *  The language abbreviation to apply. An exhaustive list of all valid languages
     *  can be found **[here](https://github.com/annexare/Countries/blob/master/data/languages.json)**.
     */
    Visao.prototype.changeLanguage = function (language) {
      this.executeAction(
        {
          type: exports.ViewerAPIMessage.UPDATE_LANGUAGE,
          payload: {
            language: language
          }
        },
        exports.ViewerStatus.MOUNTED
      );
    };
    /**
     * #### Description
     * > Change the displayed configuration/variant
     * of the current 3D model if it can be found.
     *
     * #### Usage:
     * A 3D model of a shoe could have 2 different
     * configuration/variant of `green` and `blue` each displaying the same shoe in the matching color.
     *
     * > :warning: **{@link ViewerStatus}**  must be at **{@link ViewerStatus.LOADED}** to execute.
     *
     * ```typescript
     * // Change the model's color from blue to green
     * visao.showModelVariant('green');
     * ```
     *
     *  @param modelVariant
     *  A unique key that can be matched inside the current 3D model schema.
     */
    Visao.prototype.showModelVariant = function (modelVariant) {
      this.executeAction(
        {
          type: exports.ViewerAPIMessage.UPDATE_VARIANT,
          payload: {
            modelVariant: modelVariant
          }
        },
        exports.ViewerStatus.LOADED
      );
    };
    /**
     * #### Description
     * > Get the 3D model's variant/configuration information from the Visao viewer.
     * A configuration/variant is a structure with selected properties within the 3D model file,
     * which will determine how the 3D model will be displayed.
     *
     * #### Usage:
     * > :warning: **{@link ViewerStatus}**  must be at **{@link ViewerStatus.LOADED}** to execute.
     *
     * A 3D model of a shoe could have 2 different
     * configuration/variant of `green` and `blue` each displaying the same shoe in the matching color.
     *
     * ```typescript
     * // Get the configuration/variant information
     * let variantDisplayed;
     * let availableVariants;
     * visao.showModelVariant((info) => {
     *   variantDisplayed = info.variant;
     *   availableVariants = info.variants;
     *   // Do something with the data
     * });
     * ```
     *
     *  @param callback
     *  The configuration/variant information will be the parameter of the provided callback once called.
     */
    Visao.prototype.getVariantInformation = function (callback) {
      var callbackId = this.callbacks.addTo(
        exports.ViewerAPIMessage.GET_VARIANT_INFO,
        callback
      );
      this.executeAction(
        {
          type: exports.ViewerAPIMessage.GET_VARIANT_INFO,
          callbackId: callbackId
        },
        exports.ViewerStatus.LOADED
      );
    };
    /**
     * #### Description
     * > Starts the augmented reality feature.
     *
     * #### Usage
     * > :warning: **{@link ViewerStatus}**  must be at **{@link ViewerStatus.LOADED}** to execute. This feature only
     *  works on devices supporting augmented reality.
     *
     * ```typescript
     * visao.startAR();
     * ```
     */
    Visao.prototype.startAR = function () {
      this.executeAction(
        {
          type: exports.ViewerAPIMessage.START_AR
        },
        exports.ViewerStatus.LOADED
      );
    };
    /**
     * #### Description
     * > Reset the viewer's camera to its original state.
     *
     * #### Usage
     *  > :warning: **{@link ViewerStatus}**  must be at **{@link ViewerStatus.LOADED}** to execute.
     *
     * ```typescript
     * visao.resetCamera();
     * ```
     */
    Visao.prototype.resetCamera = function () {
      this.executeAction(
        {
          type: exports.ViewerAPIMessage.RESET_CAMERA
        },
        exports.ViewerStatus.LOADED
      );
    };
    /**
     * #### Description
     * > Locks the viewer's camera controls.
     *
     * #### Usage
     *  > :warning: **{@link ViewerStatus}**  must be at **{@link ViewerStatus.LOADED}** to execute.
     *
     * ```typescript
     * visao.lockCamera();
     * ```
     */
    Visao.prototype.lockCamera = function () {
      this.executeAction(
        {
          type: exports.ViewerAPIMessage.LOCK_CAMERA
        },
        exports.ViewerStatus.LOADED
      );
    };
    /**
     * #### Description
     * > Unlocks the viewer's camera controls.
     *
     * #### Usage
     *  > :warning: **{@link ViewerStatus}**  must be either at **{@link ViewerStatus.MOUNTED}**,
     *  **{@link ViewerStatus.LOADING}**, **{@link ViewerStatus.LOADED}** to execute.
     *
     * ```typescript
     * visao.unlockCamera();
     * ```
     */
    Visao.prototype.unlockCamera = function () {
      this.executeAction(
        {
          type: exports.ViewerAPIMessage.UNLOCK_CAMERA
        },
        exports.ViewerStatus.MOUNTED
      );
    };
    /**
     * #### Description
     * > Shows the viewer's help panel.
     *
     * #### Usage
     *  > :warning: **{@link ViewerStatus}**  must be either at **{@link ViewerStatus.MOUNTED}**,
     *  **{@link ViewerStatus.LOADING}**, **{@link ViewerStatus.LOADED}** to execute.
     *
     * ```typescript
     * visao.showHelp();
     * ```
     */
    Visao.prototype.showHelp = function () {
      this.executeAction(
        {
          type: exports.ViewerAPIMessage.SHOW_HELP
        },
        exports.ViewerStatus.MOUNTED
      );
    };
    /**
     * #### Description
     * > Closes the viewer's help panel.
     *
     * #### Usage
     *  > :warning: **{@link ViewerStatus}**  must be either at **{@link ViewerStatus.MOUNTED}**,
     *  **{@link ViewerStatus.LOADING}**, **{@link ViewerStatus.LOADED}** to execute.
     *
     * ```typescript
     * visao.closeHelp();
     * ```
     */
    Visao.prototype.closeHelp = function () {
      this.executeAction(
        {
          type: exports.ViewerAPIMessage.CLOSE_HELP
        },
        exports.ViewerStatus.MOUNTED
      );
    };
    /**
     * #### Description
     * > Shows the viewer's QR code panel.
     *
     * #### Usage
     *  > :warning: **{@link ViewerStatus}**  must be either at **{@link ViewerStatus.MOUNTED}**,
     *  **{@link ViewerStatus.LOADING}**, **{@link ViewerStatus.LOADED}** to execute.
     *
     * ```typescript
     * visao.showQR();
     * ```
     */
    Visao.prototype.showQR = function () {
      this.executeAction(
        {
          type: exports.ViewerAPIMessage.SHOW_QR
        },
        exports.ViewerStatus.MOUNTED
      );
    };
    /**
     * #### Description
     * > Closes the viewer's QR code panel.
     *
     * #### Usage
     *  > :warning: **{@link ViewerStatus}**  must be either at **{@link ViewerStatus.MOUNTED}**,
     *  **{@link ViewerStatus.LOADING}**, **{@link ViewerStatus.LOADED}** to execute.
     *
     * ```typescript
     * visao.closeQR();
     * ```
     */
    Visao.prototype.closeQR = function () {
      this.executeAction(
        {
          type: exports.ViewerAPIMessage.CLOSE_QR
        },
        exports.ViewerStatus.MOUNTED
      );
    };
    Visao.prototype.executeAction = function (action, statusNeeded) {
      var _a, _b;
      this.logInvalidViewerElement();
      this.logForInsufficientStatusLevel(statusNeeded);
      var message = __assign(__assign({}, action), {
        source: exports.ViewerAPIMessageSource.API
      });
      (_b =
        (_a = this.viewerElement) === null || _a === void 0
          ? void 0
          : _a.contentWindow) === null || _b === void 0
        ? void 0
        : _b.postMessage(JSON.stringify(message), "*");
    };
    Visao.prototype.logInvalidViewerElement = function () {
      if (!this.viewerElement) {
        logErrorBlock([
          'Viewer HTML element cannot be found in the DOM with the id "' +
            this.id +
            '".',
          "Make sure the visao iframe is properly mounted in the DOM before calling this method."
        ]);
      }
    };
    Visao.prototype.logForInsufficientStatusLevel = function (statusNeeded) {
      if (
        statusNeeded &&
        !this.validateStatusHasReachedNeededLevel(statusNeeded)
      ) {
        logErrorBlock([
          "The viewer is not ready for this action.",
          "Minimum viewer status needed: [" + statusNeeded + "]",
          "Current viewer status: [" + this.status + "]"
        ]);
      }
    };
    Visao.prototype.validateStatusHasReachedNeededLevel = function (
      statusNeeded
    ) {
      return statusLevels[this.status] >= statusLevels[statusNeeded];
    };
    return Visao;
  })();

  exports.Demo = Demo;
  exports.LocalizedContent = LocalizedContent;
  exports.Step = Step;
  exports.Visao = Visao;
  exports.listenToMessages = listenToMessages;
  exports.parseIncomingMessage = parseIncomingMessage;
  exports.statusLevels = statusLevels;
  exports.unListenToMessages = unListenToMessages;

  Object.defineProperty(exports, "__esModule", { value: true });
  window.VisaoAPI = exports;
});
//# sourceMappingURL=index.umd.js.map
