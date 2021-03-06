<?php
/**
 * Pi Engine (http://pialog.org)
 *
 * @link            http://code.pialog.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://pialog.org
 * @license         http://pialog.org/license.txt New BSD License
 */

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */
namespace Module\Tools\Controller\Admin;

use Pi;
use Pi\Mvc\Controller\ActionController;
use Module\Tools\Form\SocialForm;
use Module\Tools\Form\SocialFilter;

class SocialController extends ActionController
{
    public function indexAction()
    {
        // Get info
        $list = array();
        $order = array('order ASC', 'id DESC');
        $select = $this->getModel('social')->select()->order($order);
        $rowset = $this->getModel('social')->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $list[$row->id] = $row->toArray();
        }
        // Set view
        $this->view()->setTemplate('social-index');
        $this->view()->assign('list', $list);
    }

    public function updateAction()
    {
        // Get id
        $id = $this->params('id');
        // Set form
        $form = new SocialForm('social');
        $form->setAttribute('enctype', 'multipart/form-data');
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $form->setInputFilter(new SocialFilter);
            $form->setData($data);
            if ($form->isValid()) {
                $values = $form->getData();
                // Save values
                if (!empty($values['id'])) {
                    $row = $this->getModel('social')->find($values['id']);
                } else {
                    $row = $this->getModel('social')->createRow();
                }
                $row->assign($values);
                $row->save();
                // Update registry
                Pi::registry('socialList', 'tools')->clear();
                // jump
                $message = __('Social data saved successfully.');
                $this->jump(array('action' => 'index'), $message);
            }
        } else {
            if ($id) {
                $social = $this->getModel('social')->find($id)->toArray();
                $form->setData($social);
            }
        }
        // Set view
        $this->view()->setTemplate('social-update');
        $this->view()->assign('form', $form);
        $this->view()->assign('title', __('Manage social'));
    }
}