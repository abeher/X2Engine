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

Yii::import('application.models.X2Model');

/**
 * This is the model class for table "x2_leads".
 *
 * @package application.modules.x2Leads.models
 */
class X2Leads extends X2Model {

	/**
	 * Returns the static model of the specified AR class.
	 * @return X2Leads the static model class
	 */
	public static function model($className=__CLASS__) { return parent::model($className); }

	/**
	 * @return string the associated database table name
	 */
	public function tableName() { return 'x2_x2leads'; }

	public function behaviors() {
		return array_merge(parent::behaviors(),array(
			'X2LinkableBehavior'=>array(
				'class'=>'X2LinkableBehavior',
				'module'=>'x2Leads'
			),
			'ERememberFiltersBehavior' => array(
				'class' => 'application.components.ERememberFiltersBehavior',
				'defaults'=>array(),
				'defaultStickOnClear'=>false
			),
			'X2ModelConversionBehavior' => array(
				'class' => 'application.components.recordConversion.X2ModelConversionBehavior',
			),
            'ContactsNameBehavior' => array(
                'class' => 'application.components.ContactsNameBehavior',
            ),
		));
	}

	public static function getNames() {
		$arr = X2Leads::model()->findAll();
		$names = array(0=>'None');
		foreach($arr as $x2Leads)
			$names[$x2Leads->id] = $x2Leads->name;

		return $names;
	}

	public static function getX2LeadsLinks($accountId) {
		$allX2Leads = 
            X2Model::model('X2Leads')->findAllByAttributes(array('accountName'=>$accountId));

		$links = array();
		foreach($allX2Leads as $model) {
			$links[] = CHtml::link($model->name,array('/x2Leads/x2Leads/view','id'=>$model->id));
		}
		return implode(', ',$links);
	}

	public function search($resultsPerPage=null, $uniqueId=null) {
		$criteria=new CDbCriteria;
		$parameters=array('limit'=>ceil(Profile::getResultsPerPage()));
		$criteria->scopes=array('findAll'=>array($parameters));

		return $this->searchBase($criteria, $resultsPerPage);
	}

	public function searchAdmin() {
		$criteria=new CDbCriteria;

		return $this->searchBase($criteria);
	}

}
