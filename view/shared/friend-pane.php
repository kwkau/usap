<div class="row" data-sw-model="Friend_mdl frnd">
    <div class="col-lg-3 col-md-3 col-sm-2">
        <div class="frnd-pod" data-frndid="{{=frnd.id}}" data-uid="{{=frnd.user_prof.user_id}}">
            <div class="frnd-prof-pic">
                <img class="image-responsive" src="{{=frnd.user_prof.profile_pic}}" alt=""/>
                <div class="username">
                    {{=frnd.user_prof.full_name}}
                </div>
            </div>
            <div class="frnd-details">
                <ul class="text-left">
                    <li><span class="glyph">&#xf007;</span><?=$this->htmlAnchor("profile","{{=frnd.user_prof.first_name}}'s Profile","{{=frnd.user_prof.user_id}}")?></li>
                    <li><span class="glyph">&#xf015;</span><?=$this->htmlAnchor("dropzone","{{=frnd.user_prof.first_name}}'s Dropzone","{{=frnd.user_prof.user_id}}")?></li>
                </ul>
            </div>
        </div>
    </div>
</div>