<?php
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

$this->pageTitle = CHtml::encode(
                Yii::app()->settings->appName . ' - ' . Yii::t('x2Leads', 'View Lead'));

$authParams['assignedTo'] = $model->assignedTo;

$menuOptions = array(
    'index', 'create', 'view', 'edit', 'delete', 'attach', 'quotes',
    'convertToContact', 'convert', 'print',
);
$this->insertMenu($menuOptions, $model, $authParams);


Yii::app()->clientScript->registerResponsiveCssFile(
        Yii::app()->theme->baseUrl . '/css/responsiveRecordView.css');
Yii::app()->clientScript->registerCss('leadViewCss', "

#content {
    background: none !important;
    border: none !important;
}

#conversion-warning-dialog ul {
    padding-left: 25px !important;
}

");

Yii::app()->clientScript->registerScriptFile(Yii::app()->getBaseUrl() . '/js/Relationships.js');

Yii::app()->clientScript->registerScript('leadsJS', "

// widget data
$(function() {
	$('body').data('modelType', 'x2Leads');
	$('body').data('modelId', $model->id);
});

");

$themeUrl = Yii::app()->theme->getBaseUrl();
?>

<div class="page-title-placeholder"></div>
<div class="page-title-fixed-outer">
    <div class="page-title-fixed-inner">

        <div class="page-title icon x2Leads">
            <h2><span class="no-bold"><?php echo Yii::t('x2Leads', 'Leads:'); ?> </span><?php echo CHtml::encode($model->name); ?></h2>
            <?php
            echo X2Html::editRecordButton($model);
            echo X2Html::inlineEditButtons();
            ?>
        </div>
    </div>
</div>
<div id="main-column" class="half-width">
    <?php
    $this->beginWidget('CActiveForm', array(
        'id' => 'contacts-form',
        'enableAjaxValidation' => false,
        'action' => array('saveChanges', 'id' => $model->id),
    ));

    $this->renderPartial('application.components.views._detailView', array('model' => $model, 'modelName' => 'X2Leads'));
    $this->endWidget();

    $this->widget('InlineEmailForm', array(
        'attributes' => array(
            'modelName' => 'X2Leads',
            'modelId' => $model->id,
            'targetModel' => $model,
        ),
        'startHidden' => true,
    ));

    $this->widget('X2WidgetList', array('block' => 'center', 'model' => $model, 'modelType' => 'x2Leads'));
    ?>
    <div id="quote-form-wrapper">
    <?php
    $this->widget('InlineQuotes', array(
        'startHidden' => true,
        'recordId' => $model->id,
        'account' => $model->getLinkedAttribute('accountName', 'name'),
        'modelName' => X2Model::getModuleModelName()
    ));
    ?>
    </div>

<?php
$this->widget(
        'Attachments', array(
    'associationType' => 'x2Leads', 'associationId' => $model->id,
    'startHidden' => true
        )
);
?>
</div>
<div class="history half-width">
    <?php
    $this->widget('Publisher', array(
        'associationType' => 'x2Leads',
        'associationId' => $model->id,
        'assignedTo' => Yii::app()->user->getName(),
        'calendar' => false
            )
    );

    $this->widget('History', array('associationType' => 'x2Leads', 'associationId' => $model->id));
    ?>
</div>

    <?php
    $this->widget('CStarRating', array('name' => 'rating-js-fix', 'htmlOptions' => array('style' => 'display:none;')));

    $this->widget('X2ModelConversionWidget', array(
        'buttonSelector' => '#convert-lead-button',
        'targetClass' => 'Opportunity',
        'namespace' => 'Opportunity',
        'model' => $model,
    ));

    $this->widget('X2ModelConversionWidget', array(
        'buttonSelector' => '#convert-lead-to-contact-button',
        'targetClass' => 'Contacts',
        'namespace' => 'Contacts',
        'model' => $model,
    ));
    ?>
