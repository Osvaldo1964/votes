(function () {
	"use strict";

	var treeviewMenu = $('.app-menu');

	// Toggle Sidebar
	$('[data-toggle="sidebar"]').click(function (event) {
		event.preventDefault();
		$('.app').toggleClass('sidenav-toggled');
	});

	// Activate sidebar treeview toggle
	$("[data-toggle='treeview']").click(function (event) {
		event.preventDefault();
		event.stopPropagation();
		if (!$(this).parent().hasClass('is-expanded')) {
			$(this).parent().siblings().removeClass('is-expanded');
		}
		$(this).parent().toggleClass('is-expanded');
	});

	// Set initial active toggle
	$("[data-toggle='treeview.'].is-expanded").parent().toggleClass('is-expanded');

	$("[data-toggle='tooltip']").tooltip();

})();

// Helper Global para Encabezado de Reportes
function fntGetHeaderReporte() {
	return `
        <div class="row mb-4">
            <div class="col-12 text-left">
                <div style="border-left: 5px solid #E91E63; padding-left: 15px;">
                    <h4 style="margin: 0; font-weight: bold; color: #333;">CHADAN ROSADO TAYLOR</h4>
                    <span style="font-size: 14px; color: #666; font-style: italic;">Candidato a la Cámara - MAGDALENA 101</span>
                </div>
            </div>
        </div>
    `;
}
