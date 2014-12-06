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

if (typeof x2 === 'undefined') x2 = {};

x2.InlineRelationshipsWidget = (function () {

function InlineRelationshipsWidget (argsDict) {
    var defaultArgs = {
        DEBUG: x2.DEBUG && false,
        recordId: null,
        recordType: null,
        displayMode: null,
        height: null,
        ajaxGetModelAutocompleteUrl: '',
        defaultsByRelatedModelType: {}, // {<model type>: <dictionary of default attr values>}
        createUrls: {}, // {<model type>: <string>}
        dialogTitles: {}, // {<model type>: <string>}
        tooltips: {}, // {<model type>: <string>}
        hasUpdatePermissions: null,
        createRelationshipUrl: null,

        // used to determine which models the quick create button is displayed for
        modelsWhichSupportQuickCreate: []
    };
    auxlib.applyArgs (this, defaultArgs, argsDict);
    this._relationshipsGridContainer$ = $('#relationships-form');
     
    this._gridViewButton$ = $('#rel-grid-view-button');
    this._form$ = $('#new-relationship-form');
    this._relationshipManager = null;

    SortableWidget.call (this, argsDict);
}

InlineRelationshipsWidget.prototype = auxlib.create (SortableWidget.prototype);

/*
Public static methods
*/

/*
Private static methods
*/

/*
Public instance methods
*/

/*
Private instance methods
*/

/**
 * Set up quick create button for given model class
 * @param string modelType 
 */
InlineRelationshipsWidget.prototype.initQuickCreateButton = function (modelType) {
    var that = this;
    if (this._relationshipManager && 
        this._relationshipManager instanceof x2.RelationshipsManager) {

        this._relationshipManager.destructor ();
    }

    if ($.inArray (modelType, this.modelsWhichSupportQuickCreate) > -1) {
        $('#quick-create-record').css ('visibility', 'visible');
    } else {
        $('#quick-create-record').css ('visibility', 'hidden');
        return;
    }

    this._relationshipManager = new x2.RelationshipsManager ({
        element: $('#quick-create-record'),
        modelType: this.recordType,
        modelId: this.recordId,
        relatedModelType: modelType,
        createRecordUrl: this.createUrls[modelType],
        attributeDefaults: this.defaultsByRelatedModelType[modelType] || {},
        dialogTitle: this.dialogTitles[modelType],
        tooltip: this.tooltips[modelType],
        afterCreate: function (attributes) {
            $.fn.yiiGridView.update('relationships-grid');
            if (that._graphLoaded ()) {
                that._relationshipsGraph.connectNodeToInitialFocus (
                    modelType, attributes.id, 
                    typeof attributes.name === 'undefined' ? attributes.id : attributes.name);
            }
        }
    });

};

/**
 * Requests a new autocomplete widget for the specified model class, replacing the current one
 * @param string modelType
 */
InlineRelationshipsWidget.prototype._changeAutoComplete = function (modelType) {
    x2.forms.inputLoading ($('#inline-relationships-autocomplete-container'));
    $.ajax ({
        type: 'GET',
        url: this.ajaxGetModelAutocompleteUrl,
        data: {
            modelType: modelType
        },
        success: function (data) {

            // remove span element used by jQuery widget
            $('#inline-relationships-autocomplete-container input').
                first ().next ('span').remove ();
            // replace old autocomplete with the new one
            $('#inline-relationships-autocomplete-container input').first ().replaceWith (data); 
 
            // remove the loading gif
            x2.forms.inputLoadingStop ($('#inline-relationships-autocomplete-container'));
        }
    });
};

/**
 * submits relationship create form via AJAX, performs validation 
 */
InlineRelationshipsWidget.prototype._submitCreateRelationshipForm = function () {
    var that = this; 
    $('.record-name-autocomplete').removeClass ('error');
    var error = false;

    if ($('#RelationshipModelId').val() === '') {
        that.DEBUG && console.log ('model id is not set');
        error = true;
    } else if (isNaN (parseInt($('#RelationshipModelId').val(), 10))) {
        that.DEBUG && console.log ('model id is NaN');
        error = true;
    } else if($('.record-name-autocomplete').val() === '') {
        that.DEBUG && console.log ('second name autocomplete is not set');
        error = true;
    }
    if (error) {
        $('.record-name-autocomplete').addClass ('error');
        return false;
    }
    that._form$.slideUp (200);

    var recordId = $('#RelationshipModelId').val ();
    var recordType = $('#relationship-type').val ();
    var recordName = that._form$.find ('.record-name-autocomplete').val ();

    $.ajax ({
        url: this.createRelationshipUrl,
        type: 'POST', 
        data: $('#new-relationship-form').serializeArray (),
        success: function (data) {
            if(data === 'duplicate') {
                alert('Relationship already exists.');
            } else if(data === 'success') {
                $.fn.yiiGridView.update('relationships-grid');
                var count = parseInt ($('#relationship-count').html (), 10);
                $('#relationship-count').html (count + 1);
                that._form$.find ('.record-name-autocomplete').val ();
                $('#RelationshipModelId').val('');
                $('#firstLabel').val('');
                $('#secondLabel').val('');
                 
            }
        }
    });
};

/**
 * Sets up create form submission button behavior 
 */
InlineRelationshipsWidget.prototype._setUpCreateFormSubmission = function () {
    var that = this;

    $('#add-relationship-button').on('click', function () {
        that._submitCreateRelationshipForm ();
        return false;
    });
};



InlineRelationshipsWidget.prototype._setUpSettingsBehavior = function () {
    // detach the CGridView summary and move it to the widget settings menu
    var settingsMenu$ = $(this.elementSelector + ' .widget-settings-menu-content');
    settingsMenu$.find ('ul').remove (); // remove unneeded default element
    settingsMenu$.append (this.contentContainer.find ('.summary').detach ());

    SortableWidget.prototype._setUpSettingsBehavior.call (this);
};

InlineRelationshipsWidget.prototype._setUpPageSizeSelection = function () {
    var that = this;
    $('#resultsPerPagerelationships-grid').change (function () {
        that.setProperty ('pageSize', $(this).val ());
    });
};

InlineRelationshipsWidget.prototype._changeMode = function (mode) {
    var form$ = $('#relationships-form');
    if (mode === 'simple') {
        form$.addClass ('simple-mode');
        form$.removeClass ('full-mode');
    } else {
        form$.removeClass ('simple-mode');
        form$.addClass ('full-mode');
    }
};

InlineRelationshipsWidget.prototype._setUpModeSelection = function () {
    var that = this;
    this.element.find ('a.simple-mode, a.full-mode').click (function () {
        if ($(this).hasClass ('disabled-link')) return false;
        var newMode = $(this).hasClass ('simple-mode') ? 'simple' : 'full';
        that.setProperty ('mode', newMode);
        $(this).siblings ().removeClass ('disabled-link');
        $(this).addClass ('disabled-link');
        that._changeMode (newMode);
        return false;
    });
};



InlineRelationshipsWidget.prototype._displayGrid = function () {
     
    this._relationshipsGridContainer$.show ();
     
    this._gridViewButton$.hide ();
    this.element.find ('.ui-resizable-handle').hide ();
    $(this.contentContainer).attr ('style', '');
    this.setProperty ('displayMode', 'grid');
    this.displayMode = 'grid';
};







InlineRelationshipsWidget.prototype._afterStop = function () {
    var that = this; 
    var savedHeight = that.element.height ();
    if (this._form$.is (':visible'))
        savedHeight -= this._form$.height () + 12;
    that.setProperty ('height', savedHeight);
};



InlineRelationshipsWidget.prototype._setUpNewRelationshipsForm = function () {
    var that = this;
    $('#relationship-type').change (function () {
        that.initQuickCreateButton ($(this).val ()); 
        that._changeAutoComplete ($(this).val ());
    }).change ();
    
    $('#secondLabel').hide();
    $('#myName').hide();
    $('#RelationshipLabelButton').bind('click', function(){
        $('#RelationshipLabelButton').toggleClass('fa fa-long-arrow-right');
        $('#RelationshipLabelButton').toggleClass('fa fa-long-arrow-left');
        $('#myName').toggle(200);
        $('#secondLabel').toggle( 200);
        var val = $('#mutual').val();
        val = (val == 'true') ? 'false' : 'true';

        $('#mutual').val(val);
    });

    $('#new-relationship-button').click (function () {
        if (that._form$.is (':visible')) {
            that._form$.slideUp (200);
        } else {
            that.contentContainer.attr ('style', '');
            that._form$.slideDown (200);
        }
    });

    this._setUpCreateFormSubmission ();
};

InlineRelationshipsWidget.prototype._init = function () {
    SortableWidget.prototype._init.call (this);
    if (this.displayMode === 'grid') this.element.find ('.ui-resizable-handle').hide ();
    this._setUpPageSizeSelection ();
    this._setUpModeSelection ();
     

    if (this.hasUpdatePermissions) this._setUpNewRelationshipsForm ();
};


return InlineRelationshipsWidget;

}) ();



