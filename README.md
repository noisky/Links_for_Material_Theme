# Links for Material Theme

Links for Material Theme 是一个 Typecho 友情链接插件，基于 Hanny 的 Links 插件修改，提供友情链接管理、分类、排序和多种前台输出方式。其中 `MATERIAL_SHOW` 会输出 Material Theme 友情链接卡片所需的 HTML 结构，并配合主题样式实现响应式卡片布局。

## 功能

- 添加、编辑、删除和分类管理友情链接。
- 支持 MySQL 和 SQLite。
- 支持链接描述、图片和自定义数据。
- 支持整行拖拽排序，松开后自动保存。
- 支持按照后台排序、每日随机输出和每次随机输出。
- 支持 PHP 方法和 `<links>` 标签输出。
- 提供 `SHOW_TEXT`、`SHOW_IMG`、`SHOW_MIX` 和 `MATERIAL_SHOW` 格式，其中 `MATERIAL_SHOW` 会生成 `.link-box`、`.thumb`、`.content` 和 `.title` 卡片结构。
- 新安装到 MySQL 时使用 InnoDB 引擎和 `utf8mb4` 字符集。

## 安装

在 Typecho 根目录执行：

```bash
cd usr/plugins
git clone https://github.com/noisky/Links_for_Material_Theme.git Links
```

或下载项目文件，将插件目录放入 `usr/plugins/Links`，然后在 Typecho 后台启用插件。

插件启用时会根据当前数据库驱动读取对应的安装或升级 SQL 文件。若文件缺失、不可读或读取失败，启用过程会直接显示包含文件路径的错误信息，便于定位部署问题。

> MySQL 的 InnoDB 和 `utf8mb4` 设置仅用于新建数据表，不会自动转换已有的友情链接表。已有安装如需迁移存储引擎或字符集，请先备份数据，再由数据库管理员单独处理。

## 后台管理

启用后进入“管理” → “友情链接”。

- “友情链接”选项卡：添加、编辑、删除和拖拽排序。
- 拖拽排序保存期间列表会进入 Loading 状态，暂时不能再次排序。
- “设置”选项卡：配置友情链接输出顺序。
- “帮助”选项卡：打开项目主页。

输出顺序设置：

| 模式 | 说明 |
| --- | --- |
| 按照后台排序 | 使用管理页面中的拖拽顺序，默认模式。 |
| 每日随机输出 | 按照站点时区每天生成固定顺序，同一天内保持不变。 |
| 每次随机输出 | 每次页面生成时重新打乱顺序。 |

## 前台调用

### PHP 方法

```php
<?php Links_Plugin::output('MATERIAL_SHOW'); ?>
```

方法签名：

```php
Links_Plugin::output($pattern = NULL, $links_num = 0, $sort = NULL);
```

- `$pattern`：输出格式或自定义 HTML 模板。
- `$links_num`：输出数量，`0` 表示不限制。
- `$sort`：链接分类，留空表示全部分类。

限制数量并指定分类：

```php
<?php Links_Plugin::output('MATERIAL_SHOW', 10, 'blog'); ?>
```

### 内容标签

```text
<links 10 blog>SHOW_TEXT</links>
```

其中 `10` 为数量，`blog` 为分类，`SHOW_TEXT` 为输出格式。分类名称建议使用英文字母、数字和下划线。

### 输出格式

| 格式 | 说明 |
| --- | --- |
| `SHOW_TEXT` | 文本链接，省略格式时的默认格式。 |
| `SHOW_IMG` | 只输出链接图片。 |
| `SHOW_MIX` | 输出图片和链接名称。 |
| `MATERIAL_SHOW` | 输出包含 `.thumb`、`.content` 和 `.title` 的 Material Theme 卡片结构。 |

所有输出结果都会包裹在 `.link-box` 容器中。自定义模板支持 `{lid}`、`{name}`、`{url}`、`{sort}`、`{title}`、`{description}`、`{image}` 和 `{user}` 占位符。

插件只负责输出卡片结构，卡片宽度、响应式列数、阴影、悬浮效果和深色模式由 Material Theme 的友情链接样式提供。使用 `MATERIAL_SHOW` 时，需要确保主题已加载对应样式。

## 版本记录

### 1.2.1（2026-09-20）

- MySQL 新安装改用 InnoDB 存储引擎和 `utf8mb4` 字符集。
- 安装与升级 SQL 改为从插件目录定位，并在文件不可读或读取失败时给出明确错误。

### 1.2.0（2026-09-12）

- 增加 `MATERIAL_SHOW` 输出模式，生成 Material Theme 友情链接卡片结构，并配合主题样式实现响应式布局、悬浮效果和深色模式适配。
- 增加后台设置选项卡和三种输出顺序模式。
- 增加整行拖拽排序、自动保存、Loading 和顶部提示。

### 1.1.3（2017-11-21）

- 修复越权漏洞。

### 1.1.2（2016-10-19）

- 修复插件重复启用时的数据表错误。

### 1.1.1（2014-12-14）

- 支持 Typecho 1.0，修正不能删除友情链接的问题。

### 1.1.0（2013-12-08）

- 支持 Typecho 0.9。

### 1.0.4（2010-06-30）

- 修正数据表前缀问题，补充 Pattern 字段。

### 1.0.3（2010-06-20）

- 增加图片、分类、自定义字段、多种输出方式和内容标签支持。

### 1.0.2（2010-05-16）

- 增加 SQLite 支持。

### 1.0.1（2009-12-27）

- 增加链接描述、数量限制和图片链接功能。

### 1.0.0（2009-12-12）

- 实现友情链接的添加、删除、修改和排序。

## 致谢

1. 感谢 [寒泥](https://www.imhan.com/typecho/) 提供原始的 Typecho Links 插件。
2. 感谢 [Hanson](https://github.com/Hanson/Links_for_Material_Theme) 对项目的修改与分享。
