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

/**
 * X2FlowAction that adds, removes or clears all tags on a record
 *
 * @package application.components.x2flow.actions
 */
class X2FlowRecordTag extends X2FlowAction {
	public $title = 'Add or Remove Tags';
	public $info = 'Enter a comma-separated list of tags to add to the record';

	public function paramRules() {
		$tagActions = array(
			'add' => Yii::t('studio','Add'),
			'remove' => Yii::t('studio','Remove'),
			'clear' => Yii::t('studio','Clear All'),
		);
		return array(
			'title' => Yii::t('studio',$this->title),
			'info' => Yii::t('studio',$this->info),
			'modelRequired' => 1,
			'options' => array(
				array(
                    'name'=>'tags',
                    'label'=>Yii::t('studio','Tags'),
                    'type'=>'tags',
                    'optional'=>true
                ),
				array('name'=>'action','label'=>Yii::t('studio','Action'),'type'=>'dropdown','options'=>$tagActions),
			));
	}

	public function execute(&$params) {
		$tags = $this->parseOption('tags',$params);

        $retVal = null;
        $model = $params['model'];
		switch($this->parseOption('action',$params)) {
			case 'add':
				$retVal = $model->addTags($tags);
                break;
			case 'remove':
				$retVal = $model->removeTags($tags);
                break;
			case 'clear':
				$retVal = $model->clearTags();
                break;
		}
        if ($retVal) {
		    if(is_subclass_of ($model,'X2Model')) {
                return array (
                    true,
                    Yii::t('studio', 'View updated record: ').$model->getLink ()
                );
            } else {
                return array (true, "");
            }
        } else {
            return array (false, "");
        }
	}
}
