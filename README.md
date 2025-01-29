# typecho-gitwidget【modified】

很简单的插件，用来在Typecho文章中显示Git项目小挂件，目前支持Gitee码云、Github

An easy pulgin to display Git repo preview card widget.Support Gitee&Github.

Github使用的是对[JoelSutherland/GitHub-jQuery-Repo-Widget](https://github.com/JoelSutherland/GitHub-jQuery-Repo-Widget)的魔改，详见github.js

使用GitHub卡片需要引入jQuery!不建议使用插件内置的jQuery!

码云使用的是官方挂件。

使用方法，直接在文章中，添加`gitwidget`短代码
```
[gitwidget type='gitee' url='SimonH/typecho-gitwidget']
[gitwidget type='github' url='JoelSutherland/GitHub-jQuery-Repo-Widget']
```
相关CSS样式可以在插件配置里修改。

# Install安装
克隆整个项目后把文件夹改名GitWidget塞进你typecho网站的`/usr/plugins`
