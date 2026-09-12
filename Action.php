<?php
class Links_Action extends Typecho_Widget implements Widget_Interface_Do
{
	private $db;
	private $options;
	private $prefix;
			
	public function insertLink()
	{
		if (Links_Plugin::form('insert')->validate()) {
			$this->response->goBack();
		}
		/** 取出数据 */
		$link = $this->request->from('name', 'url', 'sort', 'image', 'description', 'user');
		$link['order'] = $this->db->fetchObject($this->db->select(array('MAX(order)' => 'maxOrder'))->from($this->prefix.'links'))->maxOrder + 1;

		/** 插入数据 */
		$link['lid'] = $this->db->query($this->db->insert($this->prefix.'links')->rows($link));

		/** 设置高亮 */
		$this->widget('Widget_Notice')->highlight('link-'.$link['lid']);

		/** 提示信息 */
		$this->widget('Widget_Notice')->set(_t('链接 <a href="%s">%s</a> 已经被增加',
		$link['url'], $link['name']), NULL, 'success');

		/** 转向原页 */
		$this->response->redirect(Typecho_Common::url('extending.php?panel=Links%2Fmanage-links.php', $this->options->adminUrl));
	}

	public function addHannysBlog()
	{
		/** 取出数据 */
		$link = array(
			'name' => "Hanny's Blog",
			'url' => "http://www.imhan.com", 
			'description' => "寒泥 - Typecho插件开发者", 
		);
		$link['order'] = $this->db->fetchObject($this->db->select(array('MAX(order)' => 'maxOrder'))->from($this->prefix.'links'))->maxOrder + 1;

		/** 插入数据 */
		$link['lid'] = $this->db->query($this->db->insert($this->prefix.'links')->rows($link));

		/** 设置高亮 */
		$this->widget('Widget_Notice')->highlight('link-'.$link['lid']);

		/** 提示信息 */
		$this->widget('Widget_Notice')->set(_t('链接 <a href="%s">%s</a> 已经被增加',
		$link['url'], $link['name']), NULL, 'success');

		/** 转向原页 */
		$this->response->redirect(Typecho_Common::url('extending.php?panel=Links%2Fmanage-links.php', $this->options->adminUrl));
	}

	public function updateLink()
	{
		if (Links_Plugin::form('update')->validate()) {
			$this->response->goBack();
		}

		/** 取出数据 */
		$link = $this->request->from('lid', 'name', 'sort', 'image', 'url', 'description', 'user');

		/** 更新数据 */
		$this->db->query($this->db->update($this->prefix.'links')->rows($link)->where('lid = ?', $link['lid']));

		/** 设置高亮 */
		$this->widget('Widget_Notice')->highlight('link-'.$link['lid']);

		/** 提示信息 */
		$this->widget('Widget_Notice')->set(_t('链接 <a href="%s">%s</a> 已经被更新',
		$link['url'], $link['name']), NULL, 'success');

		/** 转向原页 */
		$this->response->redirect(Typecho_Common::url('extending.php?panel=Links%2Fmanage-links.php', $this->options->adminUrl));
	}

    public function deleteLink()
    {
        $lids = $this->request->filter('int')->getArray('lid');
        $deleteCount = 0;
        if ($lids && is_array($lids)) {
            foreach ($lids as $lid) {
                if ($this->db->query($this->db->delete($this->prefix.'links')->where('lid = ?', $lid))) {
                    $deleteCount ++;
                }
            }
        }
        /** 提示信息 */
        $this->widget('Widget_Notice')->set($deleteCount > 0 ? _t('链接已经删除') : _t('没有链接被删除'), NULL,
        $deleteCount > 0 ? 'success' : 'notice');
        
        /** 转向原页 */
        $this->response->redirect(Typecho_Common::url('extending.php?panel=Links%2Fmanage-links.php', $this->options->adminUrl));
    }

    public function saveSettings()
    {
        Typecho_Widget::widget('Widget_Security')->protect();

        $outputMode = (string) $this->request->get('outputMode');
        if (!in_array($outputMode, array('order', 'daily', 'request'), true)) {
            $outputMode = 'order';
        }
        $optionName = 'plugin:Links';
        $option = $this->db->fetchRow($this->db->select()->from($this->prefix.'options')
            ->where('name = ?', $optionName)->limit(1));
        $settings = array(
            'outputMode' => $outputMode,
            // 保留旧字段，便于旧版本代码继续按每日随机配置工作。
            'dailyRandom' => 'daily' === $outputMode ? '1' : '0'
        );

        if ($option && !empty($option['value'])) {
            $storedSettings = json_decode($option['value'], true);
            if (!is_array($storedSettings) && 0 === strpos($option['value'], 'a:')) {
                $storedSettings = @unserialize($option['value']);
            }
            if (is_array($storedSettings)) {
                $settings = array_merge($storedSettings, $settings);
            }
        }

        if ($option) {
            $this->db->query($this->db->update($this->prefix.'options')
                ->rows(array('value' => json_encode($settings)))
                ->where('name = ?', $optionName));
        } else {
            $this->db->query($this->db->insert($this->prefix.'options')->rows(array(
                'name' => $optionName,
                'value' => json_encode($settings),
                'user' => 0
            )));
        }

        $this->widget('Widget_Notice')->set(_t('友情链接设置已经保存'), NULL, 'success');
        $this->response->redirect(Typecho_Common::url('extending.php?panel=Links%2Fmanage-links.php', $this->options->adminUrl));
    }

    public function sortLink()
    {
        $links = $this->request->filter('int')->getArray('lid');
        if ($links && is_array($links)) {
			foreach ($links as $sort => $lid) {
				$this->db->query($this->db->update($this->prefix.'links')->rows(array('order' => $sort + 1))->where('lid = ?', $lid));
			}
        }
    }

	public function action()
	{
		$user = Typecho_Widget::widget('Widget_User');
		$user->pass('administrator');
		$this->db = Typecho_Db::get();
		$this->prefix = $this->db->getPrefix();
		$this->options = Typecho_Widget::widget('Widget_Options');
		$this->on($this->request->is('do=insert'))->insertLink();
		$this->on($this->request->is('do=addhanny'))->addHannysBlog();
		$this->on($this->request->is('do=update'))->updateLink();
		$this->on($this->request->is('do=delete'))->deleteLink();
		$this->on($this->request->is('do=save-settings'))->saveSettings();
		$this->on($this->request->is('do=sort'))->sortLink();
		$this->response->redirect($this->options->adminUrl);
	}
}
