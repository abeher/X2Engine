/*****************************************************************************************
 * X2Engine Open Source Edition is a customer relationship management program developed by
 * X2Engine, Inc. Copyright (C) 2011-2014 X2Engine Inc.
 * 
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY X2ENGINE, X2ENGINE DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
 * details.
 * 
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 * 
 * You can contact X2Engine, Inc. P.O. Box 66752, Scotts Valley,
 * California 95067, USA. or at email address contact@x2engine.com.
 * 
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 * 
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * X2Engine" logo. If the display of the logo is not reasonably feasible for
 * technical reasons, the Appropriate Legal Notices must display the words
 * "Powered by X2Engine".
 *****************************************************************************************/

/**
 * Prototype for publisher tab with hours and minutes time fields 
 */

if(typeof x2 == 'undefined')
    x2 = {};
if(typeof x2.publisher == 'undefined')
    x2.publisher = {};

x2.PublisherEventTab = (function () {

function PublisherEventTab (argsDict) {
    argsDict = typeof argsDict === 'undefined' ? {} : argsDict;
    var defaultArgs = {
    };
    auxlib.applyArgs (this, defaultArgs, argsDict);

	x2.PublisherTab.call (this, argsDict);	
}

PublisherEventTab.prototype = auxlib.create (x2.PublisherTimeTab.prototype);

/*
Public static methods
*/

/*
Private static methods
*/

/*
Public instance methods
*/

/**
 * Hide the autocomplete container after submission
 */
PublisherEventTab.prototype.reset = function () {
    x2.PublisherTab.prototype.reset.call (this);
    if ($('#association-type-autocomplete-container').length) {
        $('#association-type-autocomplete-container').hide ();
    }
};


/**
 * @param Bool True if form input is valid, false otherwise
 */
PublisherEventTab.prototype.validate = function () {
    var errors = !x2.PublisherTab.prototype.validate.call (this);

    if ($('#event-form-action-due-date').val () === '') {
        errors |= true;
        $('#event-form-action-due-date').addClass ('error');
        x2.forms.errorSummaryAppend (this._element, this.translations['startDateError']);
    }

    return !errors;
};


/*
Private instance methods
*/

return PublisherEventTab;

}) ();

