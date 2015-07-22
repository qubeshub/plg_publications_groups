<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Display sponsors on a resource page
 */
class plgPublicationsGroups extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return the alias and name for this category of content
	 *
	 * @param      object $publication 	Current publication
	 * @return     array
	 */
	public function &onPublicationSubAreas( $publication )
	{
		$areas = array();
		if ($publication->category()->_params->get('plg_groups', 1) == 1)
		{
			$areas = array(
				'groups' => Lang::txt('PLG_PUBLICATIONS_GROUPS')
			);
		}
		return $areas;
	}

	/**
	 * Return data on a resource sub view (this will be some form of HTML)
	 *
	 * @param      object $publication 	Current publication
	 * @param      string  $option    Name of the component
	 * @param      integer $miniview  View style
	 * @return     array
	 */
	public function onPublicationSub( $publication, $option, $miniview=0 )
	{
		$arr = array(
			'html'     => '',
			'metadata' => ''
		);

		// Check if our area is in the array of areas we want to return results for
		$areas = array('groups');
		if (!array_intersect( $areas, $this->onPublicationSubAreas( $publication ) )
		&& !array_intersect( $areas, array_keys( $this->onPublicationSubAreas( $publication ) ) ))
		{
			return false;
		}

		if (!$publication->groupOwner())
		{
			return $arr;
		}

		// Get recommendations
		$this->database = App::get('db');

		// Instantiate a view
		$this->view = new \Hubzero\Plugin\View(array(
			'folder'  => $this->_type,
			'element' => $this->_name,
			'name'    => 'display'
		));

		if ($miniview)
		{
			$this->view->setLayout('mini');
		}

		// Pass the view some info
		$this->view->option   		= $option;
		$this->view->publication 	= $publication;
		$this->view->params   		= $this->params;
		$this->view->group    		= $publication->groupOwner();

		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Return the output
		$arr['html'] = $this->view->loadTemplate();

		return $arr;
	}
}