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

class ContactsNameBehavior extends CActiveRecordBehavior {

    /**
     * @var bool $overrideName
     */
    public $overwriteName = true; 

	public function events() {
		return array_merge(parent::events(),array(
			'onAfterFind'=>'afterFind',
			'onBeforeSave'=>'beforeSave',
		));
	}

    /**
     * Sets the name field (full name) on record lookup
     */
    public function afterFind ($event) {
        if (isset(Yii::app()->settings)) {
            $this->setName();
        }
    }

    /**
     * Sets the name field (full name) before saving
     */
    public function beforeSave ($event) {
        if (isset(Yii::app()->settings)) {
            $this->setName();
        }
    }

    public function setName() {
        if ($this->owner->name && !$this->overwriteName) return;

        $admin = Yii::app()->settings;
        if (!empty($admin->contactNameFormat)) {
            $str = $admin->contactNameFormat;
            $str = str_replace('firstName', $this->owner->firstName, $str);
            $str = str_replace('lastName', $this->owner->lastName, $str);
        } else {
            $str = $this->owner->firstName . ' ' . $this->owner->lastName;
        }
        if ($admin->properCaseNames)
            $str = Formatter::ucwordsSpecific ($str, array('-', "'", '.'), 'UTF-8');

        $this->owner->name = $str;
    }
}

?>
