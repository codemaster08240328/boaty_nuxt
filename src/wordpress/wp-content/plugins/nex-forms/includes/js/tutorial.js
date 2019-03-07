var current_tutorial_step = 1;
jQuery(document).ready(
function()
	{
	jQuery(document).on('click','.next_tut_step',
		function()
			{
			nf_nex_tutorial();
			}
		);
	jQuery(document).on('click','.prev_tut_step',
		function()
			{
			nf_prev_tutorial();
			}
		);
	jQuery(document).on('click','.close-the-popover',
				function()
					{
					jQuery('.popover').hide('fast');
					}
				);
	jQuery('.nf_tutorial').popover(
	 	{
		html: true,
		trigger: 'manual'
		}
	);	
	jQuery('a.tutorial').click(
		function()
			{
			current_tutorial_step = 1;
			nf_run_tutorial(current_tutorial_step);	
			}
		);
	}
);

function nf_run_tutorial(step)
	{
	//alert(step);
		
	jQuery('.tutorial').find('.close-the-popover').trigger('click');
	
	jQuery('.nf_tutorial_step_'+step).popover('show');	
	setTimeout(function(){ 
	
	jQuery('.nf_tutorial_step_'+step).parent().find('.popover').addClass('tut_step_'+step).addClass('tutorial');
	jQuery('.tutorial').find('.fa-close').addClass('close-the-popover');
	}
	
	,50);
	}
function nf_nex_tutorial(){
	current_tutorial_step = (current_tutorial_step+1);
	nf_run_tutorial(current_tutorial_step);
}

function nf_prev_tutorial(){
	if((current_tutorial_step-1)!=0)
		{
		current_tutorial_step= current_tutorial_step-1;
		nf_run_tutorial(current_tutorial_step)
		}
}