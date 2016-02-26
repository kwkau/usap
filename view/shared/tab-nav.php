<ul id="tab-nav" class="nav nav-tabs nav-justified" data-sw-model="Tab_mdl tab">
    <li class="{{?tab.name == 'Forums' || tab.name == 'Profile'}}{{='active'}}{{?}}" data-tabname="{{=tab.name}}">
        <a href="{{=tab.id}}" data-toggle="tab">{{=tab.name}}
            {{?tab.close}}
            <span class="glyph close-item pull-right">&#xf00d;</span>
            {{?}}
        </a>
    </li>
</ul>