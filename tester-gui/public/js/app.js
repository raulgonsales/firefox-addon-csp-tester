/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/app.js":
/*!*****************************!*\
  !*** ./resources/js/app.js ***!
  \*****************************/
/*! no static exports found */
/***/ (function(module, exports) {

window.localStorage.setItem('selectedSites', JSON.stringify({
  'sites': {}
}));
jQuery(document).ready(function ($) {
  $('[data-toggle="tooltip"]').tooltip({
    'delay': {
      show: 500,
      hide: 200
    }
  });
  $('.test-addon-button').on('click', function () {
    var addonInfo = {
      'name': $(this).data('name'),
      'file': $(this).data('file'),
      'link': $(this).data('link'),
      'id': $(this).data('id')
    };
    testAddonBackendCall(addonInfo);
  });
  $('.test-selected.test-selected-initial-error').on('click', function () {
    var checkedAddons = JSON.parse(window.sessionStorage.getItem('selectedAddons')).addons;

    for (var id in checkedAddons) {
      if (checkedAddons.hasOwnProperty(id)) {
        var addonInfo = checkedAddons[id];
        console.log('Addon name: ' + addonInfo.name);
        console.log('initial-error testing started.');
        testAddonBackendCall(addonInfo);
      }
    }
  });
  $('.test-selected.test-selected-content-scripts-analyzing').on('click', function () {
    new Promise(function (resolve) {
      $("#sitesListModal").modal('show');
      $('#sitesListModal .btn-confirm').click(function () {
        resolve();
      });
    }).then(function () {
      var checkedAddons = JSON.parse(window.localStorage.getItem('selectedAddons')).addons;

      for (var addonId in checkedAddons) {
        if (checkedAddons.hasOwnProperty(addonId)) {
          var addonInfo = checkedAddons[addonId];
          var checkedSites = JSON.parse(window.localStorage.getItem('selectedSites')).sites;
          analyzeAddonContentScript(addonInfo, checkedSites);
        }
      }
    });
  });
  $('.report-all').on('click', function () {
    renderReportForAll(getReportForAll());
    $('#show_all_report').removeClass('hidden').on('click', function () {
      $('#reportAllModal').modal('toggle');
    });
    $('#reportAllModal').modal('toggle');
  });
  $('.check-addon').on('change', function () {
    if (window.sessionStorage.getItem('selectedAddons') === null) {
      window.sessionStorage.setItem('selectedAddons', JSON.stringify({
        'addons': {}
      }));
    }

    var selectedAddonsStorage = JSON.parse(window.sessionStorage.getItem('selectedAddons'));
    var checkedAddon = getAddonInfoFromCheckbox(this);

    if (this.checked) {
      selectedAddonsStorage.addons[checkedAddon.id] = checkedAddon;
    } else {
      delete selectedAddonsStorage.addons[checkedAddon.id];
    }

    window.sessionStorage.setItem('selectedAddons', JSON.stringify(selectedAddonsStorage));
  });
  $('.site-checkbox').on('change', function () {
    var selectedSitesStorage = JSON.parse(window.localStorage.getItem('selectedSites'));

    if (this.checked) {
      selectedSitesStorage.sites[$(this).data('id')] = $(this).data('matching-url');
    } else {
      delete selectedSitesStorage.sites[$(this).data('id')];
    }

    window.localStorage.setItem('selectedSites', JSON.stringify(selectedSitesStorage));
  });
  $('#select_all_addons').on('click', function () {
    $('.check-addon').each(function () {
      this.checked = true;

      if (window.sessionStorage.getItem('selectedAddons') === null) {
        window.sessionStorage.setItem('selectedAddons', JSON.stringify({
          'addons': {}
        }));
      }

      var selectedAddonsStorage = JSON.parse(window.sessionStorage.getItem('selectedAddons'));
      var checkedAddon = getAddonInfoFromCheckbox(this);
      selectedAddonsStorage.addons[checkedAddon.id] = checkedAddon;
      window.sessionStorage.setItem('selectedAddons', JSON.stringify(selectedAddonsStorage));
    });
  });
  $('.addons-deselect-button').on('click', function () {
    if ($(this).data('type') === 'current') {
      $('.check-addon').each(function () {
        this.checked = false;

        if (window.sessionStorage.getItem('selectedAddons') === null) {
          return false;
        }

        var selectedAddonsStorage = JSON.parse(window.sessionStorage.getItem('selectedAddons'));
        var checkedAddon = getAddonInfoFromCheckbox(this);

        if (selectedAddonsStorage.addons.hasOwnProperty(checkedAddon.id)) {
          delete selectedAddonsStorage.addons[checkedAddon.id];
        }

        window.sessionStorage.setItem('selectedAddons', JSON.stringify(selectedAddonsStorage));
      });
    } else if ($(this).data('type') === 'all') {
      $('.check-addon').each(function () {
        this.checked = false;

        if (window.sessionStorage.getItem('selectedAddons') !== null) {
          window.sessionStorage.removeItem('selectedAddons');
        }
      });
    }
  });
});

function getAddonInfoFromCheckbox(checkbox) {
  var addonElementWrapper = $(checkbox).parent();
  var addonTestButton = addonElementWrapper.find('.test-addon-button');
  return {
    'id': addonTestButton.data('id'),
    'name': addonTestButton.data('name'),
    'file': addonTestButton.data('file'),
    'link': addonTestButton.data('link')
  };
}

function saveContentScriptsInfo(data, addonId) {
  $.ajax({
    method: "POST",
    url: "http://localhost:998/api/save-content-scripts-info",
    data: {
      data: data,
      addon_id: addonId
    },
    datatype: 'application/json',
    crossDomain: true
  }).done(function (msg) {
    console.log(msg);
  });
}

function analyzeAddonContentScript(addonInfo, sitesMatching) {
  $.ajax({
    method: "POST",
    url: "http://localhost:996/test/content-scripts-analyzing",
    data: {
      name: addonInfo.name,
      file: addonInfo.file,
      link: addonInfo.link,
      sites_matching: JSON.stringify(sitesMatching)
    },
    datatype: 'application/json',
    crossDomain: true,
    beforeSend: function beforeSend() {
      console.log('--------- Analyze content scripts for ' + addonInfo.name + ' -------------');
    }
  }).done(function (response) {
    console.log(response);
    console.log('Content script for ' + addonInfo.name + ' successfully analyzed');
    saveContentScriptsInfo(response, addonInfo.id);
  });
}

function testAddonBackendCall(addonInfo) {
  $.ajax({
    async:false,
    method: "POST",
    url: "http://localhost:998/api/backend-call/on-start-test",
    data: {
      addon_name: addonInfo.name,
      addon_file: addonInfo.file,
      addon_link: addonInfo.link,
      addon_id: addonInfo.id
    },
    datatype: 'application/json',
    crossDomain: true
  }).done(function (msg) {
    console.log(msg);
  });
}

function getReportForAll() {
  var response = null;
  $.ajax({
    async: false,
    method: "GET",
    url: "http://localhost:998/api/report-for-all",
    datatype: 'application/json',
    crossDomain: true
  }).done(function (msg) {
    response = msg;
  });
  return response;
}

function renderReportForSelected(response) {
  $.ajax({
    method: "POST",
    url: "http://localhost:998/api/render-report",
    data: {
      data: response
    },
    datatype: 'application/json',
    crossDomain: true
  }).done(function (msg) {
    $('body').append(msg);
    $('#reportModal').modal('toggle').on('hidden.bs.modal', function (e) {
      $(this).empty().remove();
    });
  });
}

function renderReportForAll(response) {
  MODAL = document.getElementById('reportAllModal').getElementsByClassName('modal-body').item(0);
  var addonsInfo = JSON.parse(response),
      addonsCount = addonsInfo['count'],
      byErrors = addonsInfo['by_errors'];
  var x = ["initial_error", "no_error"];
  var y = [byErrors['initial_error']['count'], byErrors['no_error']['count']];
  var data = [{
    histfunc: "sum",
    y: y,
    x: x,
    type: "histogram",
    name: "Addons count by error type"
  }];
  Plotly.newPlot(MODAL, data);
  $('#reportAllModal .count-all').html(addonsCount);
}

/***/ }),

/***/ "./resources/sass/app.scss":
/*!*********************************!*\
  !*** ./resources/sass/app.scss ***!
  \*********************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 0:
/*!*************************************************************!*\
  !*** multi ./resources/js/app.js ./resources/sass/app.scss ***!
  \*************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! /var/www/resources/js/app.js */"./resources/js/app.js");
module.exports = __webpack_require__(/*! /var/www/resources/sass/app.scss */"./resources/sass/app.scss");


/***/ })

/******/ });
