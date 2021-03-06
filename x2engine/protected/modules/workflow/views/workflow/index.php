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

Yii::app()->clientScript->registerCss('workflowIndexCss',"

#workflow-grid .page-title.workflow .x2-grid-view-controls-buttons {
    position: relative;
    top: -4px;
}

");

$this->setPageTitle(Yii::t('workflow', '{process}', array(
    '{process}' => Modules::displayName(false)
)));

$menuOptions = array(
    'index', 'create',
);
$this->insertMenu($menuOptions);

?>
<div class='flush-grid-view'>
<?php

$this->widget('X2GridViewGeneric', array(
    'id' => 'workflow-grid',
	'dataProvider'=>$dataProvider,
    'baseScriptUrl'=>  
        Yii::app()->request->baseUrl.'/themes/'.Yii::app()->theme->name.'/css/gridview',
    'title'=>Yii::t('workflow','{processes}', array(
        '{processes}' => Modules::displayName())),
    'template'=> '<div class="page-title icon workflow">{title}'.
        '{buttons}{summary}</div>{items}{pager}',
	'summaryText' => Yii::t('app','<b>{start}&ndash;{end}</b> of <b>{count}</b>'),
    'buttons' => array ('autoResize'),
	'enableSorting'=>false,
	'gvSettingsName'=>'workflowIndex',
    'defaultGvSettings' => array (
        'name' => 240,
        'isDefault' => 100,
        'stages' => 100,
    ),
	'columns'=>array(
		array(
			'name'=>'name',
			'value'=>'CHtml::link(CHtml::encode($data->name),array("view","id"=>$data->id))',
			'type'=>'raw',
		),
		array(
			'name'=>'isDefault',
			'value'=>'$data->isDefault? Yii::t("app","Yes") : ""',
			'type'=>'raw',
		),
		array(
			'header'=>Yii::t('workflow','Stages'),
			'name'=>'stages',
			'value'=>'X2Model::model("WorkflowStage")->countByAttributes(array("workflowId"=>$data->id))',
			'type'=>'raw',
		),
	),
)); ?>
</div>
