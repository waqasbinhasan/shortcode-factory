		<div id="scf-shortcode-preview" style="display: none;">
			<code class="scf-preview"></code>
			<span class="notes">
				Copy the shortcode and paste in any editable area.<br>
				Turn off "Display shortcode" to insert directly into the editor.<br>
			</span>
			<button type="button" id="scf-hide-preview" style="cursor: pointer; margin-top: 10px;"><?=__('Close', 'shortcode-factory')?></button>
			<script type="text/javascript">
				$("#scf-hide-preview").on("click", function(e){
					e.preventDefault();
					
					$("#scf-shortcode-preview").hide();
				});
			</script>
		</div>
	</div>
</div>