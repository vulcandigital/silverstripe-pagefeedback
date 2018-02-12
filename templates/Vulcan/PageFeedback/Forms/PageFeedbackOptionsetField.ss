<ul $AttributesHTML>
	<% loop $Options %>
		<li class="$Class pagefeedback">
			<input id="$ID" class="radio pagefeedback-option-$Pos" name="$Name" type="radio" value="$Value.ATT" <% if $Up.Required %>required<% end_if %> />
			<label for="$ID">$Title</label>
		</li>
	<% end_loop %>
</ul>
