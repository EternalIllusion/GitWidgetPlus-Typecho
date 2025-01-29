<?php

if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 * Git Widget魔改版<span style='border-radius: 2px;padding: .10rem .20rem;margin: 0 .40rem 0 .40rem;position: relative;background-color: #E040FB;color: #fff;'><a href="http://eterill.us.kg" style="color:#fff;text-decoration:none;">by EternalIllusion</a></span>｜使用方法详见插件设置
 * 
 *
 * @package GitWidget
 * @author Simon.H
 * @version Mod1.2
 * @link http://ywy.me
 */
class GitWidget_Plugin implements Typecho_Plugin_Interface
{
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     *
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate()
    {
        Typecho_Plugin::factory('Widget_Abstract_Contents')->contentEx = array('GitWidget_Plugin','parselabelg');
        Typecho_Plugin::factory('Widget_Abstract_Contents')->excerptEx = array('GitWidget_Plugin','parselabelg');
        Typecho_Plugin::factory('Widget_Archive')->footer = array('GitWidget_Plugin','insertjs');
        Typecho_Plugin::factory('Widget_Archive')->footer = array('GitWidget_Plugin','footerjs');
    }
    
    /**
     * 内容标签替换
     * 
     * @param string $content
     * @return string
     */
    public static function parselable($content, $widget, $lastResult)
    {
        $content = empty($lastResult) ? $content : $lastResult;
        if ($widget instanceof Widget_Archive) {
            // 没有标签直接返回
            if ( false === strpos( $content, '[' ) ) {
                return $content;
            }
            $tags = self::parseTag($content, 'gitwidget');
            foreach($tags as $tag) {
                //$content .= $tag[0];
                $content = self::parseAndReplaceTag($content, $tag[0]);
            }
        }
        return $content;
    }
    
    private static function parseAndReplaceTag ($content, $tag) {
        // (\w+)=(?:['"]([^\["\']+?)['"]|(\w+))
        $regex = "/(\w+)=(?:['\"]([^\[\"\']+?)['\"]|(\w+))/";
        $match = array();
        $atts = array();
        //$content .= $tag;
        preg_match_all($regex, $tag, $match, PREG_SET_ORDER);
        foreach($match as $item) {
          if(true === isset($item[3])) {
            $atts[$item[1]] = $item[3];
          }
          else {
            $atts[$item[1]] = $item[2];
          }
        }
        if(isset($atts['skip'])) {
            return $content;
        }
        //$content .= $atts['type'];
        if (isset($atts['type']) && isset($atts['url'])) {
            $regexTag = sprintf('/%s/', preg_quote($tag, '/'));
            $replace_with = '';
            if($atts['type'] === 'gitee') {
                $replace_with .=  "<script src='//gitee.com/".$atts['url']."/widget_preview'></script>";
                $replace_with .= '<style>';
                $replace_with .= Typecho_Widget::widget('Widget_Options')->plugin('GitWidget')->gitee_css;
                $replace_with .= '</style>';
            }elseif($atts['type'] === 'github'){
                $replace_with .=  "<script>var needGithubWidget=1;</script>";
                $replace_with .= '<div class="github-widget" data-repo="'.$atts['url'].'"></div>';
                $replace_with .= '<style>';
                $replace_with .= Typecho_Widget::widget('Widget_Options')->plugin('GitWidget')->github_css;
                $replace_with .= '</style>';
            }
            $content = preg_replace($regexTag, $replace_with, $content, 1);
        }
        return $content;
    }
    
    private static function parseTag($content, $tagnames = null ) {
        $regex = "/\[{$tagnames}[^\]]*?\]/";
        $match = array();
        preg_match_all($regex, $content, $match, PREG_SET_ORDER);
        return $match;
    }
    
    public static function footerjs(){
        echo '<script language="javascript">if (typeof(needGithubWidget) != "undefined") {
            var script = document.createElement("script");
            script.src = "' . Helper::options()->pluginUrl . '/GitWidget/github.js"
            document.body.appendChild(script);
        }</script>';
    }

    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     *
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate(){}

    /**
     * 获取插件配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form)
    {
        /** 样式表 */
        $giteecss = new Typecho_Widget_Helper_Form_Element_Textarea('gitee_css', NULL, '.pro_name a{color: #4183c4;}
.osc_git_title{background-color: #fff;}
.osc_git_box{background-color: #fff;}
.osc_git_box{border-color: #E3E9ED;}
.osc_git_info{color: #666;}
.osc_git_main a{color: #9B9B9B;}
.osc_git_footer{display:none;}', _t('Gitee样式表'));
        $form->addInput($giteecss);
        $githubcss = new Typecho_Widget_Helper_Form_Element_Textarea('github_css', NULL, '', _t('Github样式表'));
        $form->addInput($githubcss);
        $importjq = new Typecho_Widget_Helper_Form_Element_Radio(
            'importjq', 
            array('1' => '引入', '0' => '不引入'),
            '0', 
            _t('自动引入jQuery'), 
            _t('选择是否在自动引入jQuery（GitHub卡片需要，不建议在这引用，容易不工作）。')
        );
        $form->addInput($importjq);
?>
<p>
<h3>使用方法：</h3><br />
直接在文章中，添加gitwidget短代码:<br />
[gitwidget type='gitee' url='SimonH/typecho-gitwidget']<br />
[gitwidget type='github' url='JoelSutherland/GitHub-jQuery-Repo-Widget']<br />
type:设置使用gitee还是github ｜ url:项目仓库地址<br />
<br />
</p>
<?php
    }

    /**
     * 个人用户的配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form){}
    
    /**
	 * 输出js脚本
	 * 
	 * @access public
	 * @return void
	 */
	public static function insertjs($widget)
	{
		$options = Helper::options();
		$settings = $options->plugin('GitWidget');
		//jquery模式
		if ($settings->importjq) {
		    if ($widget->is('single')) {
			    echo ($settings->jqmode ? '<script type="text/javascript">
window.jQuery || document.write(\'<script type="text/javascript" src="https://cdn.bootcss.com/jquery/3.3.1/jquery.min.js"><\/script>\')</script>' : '');
		    }
		}
	}
}
?>
