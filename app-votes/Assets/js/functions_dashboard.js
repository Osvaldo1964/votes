
document.addEventListener('DOMContentLoaded', function () {
    fntGetDashboard();
});

async function fntGetDashboard() {
    try {
        const data = await fetchData(BASE_URL_API + '/dashboard/getResumen');

        if (data && data.status) {
            let res = data.data;

            // Widgets
            if (document.getElementById('lblTotalElectores')) document.getElementById('lblTotalElectores').innerText = res.total_electores;
            if (document.getElementById('lblTotalInscritos')) document.getElementById('lblTotalInscritos').innerText = res.total_inscritos; // NUEVO
            if (document.getElementById('lblTotalLideres')) document.getElementById('lblTotalLideres').innerText = res.total_lideres;
            if (document.getElementById('lblTotalVotos')) document.getElementById('lblTotalVotos').innerText = res.total_votos;
            if (document.getElementById('lblMetaGlobal')) document.getElementById('lblMetaGlobal').innerText = res.meta_global;

            if (document.getElementById('lblPorcentajeMeta')) {
                document.getElementById('lblPorcentajeMeta').innerText = res.porcentaje_meta + "%";
            }

            // --- Gráfico Top Líderes ---
            let lideresNombres = [];
            let lideresCant = [];
            if (res.top_lideres) {
                lideresNombres = res.top_lideres.map(l => l.nombre);
                lideresCant = res.top_lideres.map(l => l.cantidad);
            }

            // Calcular Total de Líderes para Porcentajes
            let totalLideres = lideresCant.reduce((a, b) => parseInt(a) + parseInt(b), 0);

            if (document.getElementById('chartLideres')) {
                var ctxLideres = document.getElementById('chartLideres').getContext('2d');
                new Chart(ctxLideres, {
                    type: 'horizontalBar',
                    data: {
                        labels: lideresNombres,
                        datasets: [{
                            label: 'Electores Registrados',
                            data: lideresCant,
                            backgroundColor: [
                                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
                                '#FF9F40', '#C9CBCF', '#FF6384', '#36A2EB', '#FFCE56',
                                '#00A65A', '#DC3545', '#6C757D', '#17A2B8', '#F012BE'
                            ],
                            borderColor: [
                                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
                                '#FF9F40', '#C9CBCF', '#FF6384', '#36A2EB', '#FFCE56',
                                '#00A65A', '#DC3545', '#6C757D', '#17A2B8', '#F012BE'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        legend: {
                            labels: {
                                boxWidth: 0 // Ocultar el cuadro de color de la leyenda
                            }
                        },
                        scales: {
                            xAxes: [{ ticks: { beginAtZero: true } }]
                        },
                        tooltips: {
                            callbacks: {
                                label: function (tooltipItem, data) {
                                    let value = data.datasets[0].data[tooltipItem.index];
                                    let percentage = totalLideres > 0 ? ((value / totalLideres) * 100).toFixed(1) + '%' : '0%';
                                    return 'Electores: ' + value + ' (' + percentage + ')';
                                }
                            }
                        },
                        animation: {
                            onComplete: function () {
                                var chartInstance = this.chart,
                                    ctx = chartInstance.ctx;

                                ctx.font = Chart.helpers.fontString(11, 'bold', Chart.defaults.global.defaultFontFamily);
                                ctx.textBaseline = 'middle';

                                this.data.datasets.forEach(function (dataset, i) {
                                    var meta = chartInstance.controller.getDatasetMeta(i);
                                    meta.data.forEach(function (bar, index) {
                                        var data = dataset.data[index];
                                        // Calcular porcentaje
                                        var percentage = totalLideres > 0 ? ((data / totalLideres) * 100).toFixed(1) + '%' : '0%';
                                        var labelText = data + " (" + percentage + ")";

                                        // Determinar si el texto cabe dentro de la barra
                                        var textWidth = ctx.measureText(labelText).width;
                                        var padding = 5;
                                        var barWidth = bar._model.x;

                                        if (barWidth > (textWidth + padding + 10)) {
                                            // Texto ADENTRO
                                            ctx.fillStyle = '#fff'; // Blanco para contraste con Teal
                                            ctx.textAlign = 'right';
                                            ctx.fillText(labelText, bar._model.x - padding, bar._model.y);
                                        } else {
                                            // Texto AFUERA
                                            ctx.fillStyle = '#000';
                                            ctx.textAlign = 'left';
                                            ctx.fillText(labelText, bar._model.x + padding, bar._model.y);
                                        }
                                    });
                                });
                            }
                        },
                        layout: {
                            padding: {
                                right: 60
                            }
                        }
                    }
                });
            }

            // --- Gráfico Distribución Municipios ---
            let muniNombres = [];
            let muniCant = [];
            if (res.dist_municipios) {
                muniNombres = res.dist_municipios.map(m => m.municipio);
                muniCant = res.dist_municipios.map(m => parseInt(m.cantidad));
            }

            // Calcular Total para Porcentajes
            let totalMuni = muniCant.reduce((a, b) => a + b, 0);

            if (document.getElementById('chartMunicipios')) {
                var ctxMuni = document.getElementById('chartMunicipios').getContext('2d');
                new Chart(ctxMuni, {
                    type: 'horizontalBar',
                    data: {
                        labels: muniNombres,
                        datasets: [{
                            label: 'Electores Registrados',
                            data: muniCant,
                            backgroundColor: [
                                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
                                '#FF9F40', '#C9CBCF', '#FF6384', '#36A2EB', '#FFCE56',
                                '#00A65A', '#DC3545', '#6C757D', '#17A2B8', '#F012BE'
                            ],
                            borderColor: [
                                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
                                '#FF9F40', '#C9CBCF', '#FF6384', '#36A2EB', '#FFCE56',
                                '#00A65A', '#DC3545', '#6C757D', '#17A2B8', '#F012BE'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        legend: {
                            labels: {
                                boxWidth: 0 // Ocultar el cuadro de color
                            }
                        },
                        scales: {
                            xAxes: [{
                                ticks: { beginAtZero: true }
                            }]
                        },
                        tooltips: {
                            callbacks: {
                                label: function (tooltipItem, data) {
                                    let value = data.datasets[0].data[tooltipItem.index];
                                    let percentage = totalMuni > 0 ? ((value / totalMuni) * 100).toFixed(1) + '%' : '0%';
                                    return data.labels[tooltipItem.index] + ': ' + value + ' (' + percentage + ')';
                                }
                            }
                        },
                        animation: {
                            onComplete: function () {
                                var chartInstance = this.chart,
                                    ctx = chartInstance.ctx;

                                ctx.font = Chart.helpers.fontString(10, 'bold', Chart.defaults.global.defaultFontFamily);
                                ctx.textAlign = 'left';
                                ctx.textBaseline = 'middle';
                                ctx.fillStyle = '#000';

                                this.data.datasets.forEach(function (dataset, i) {
                                    var meta = chartInstance.controller.getDatasetMeta(i);
                                    meta.data.forEach(function (bar, index) {
                                        var data = dataset.data[index];
                                        // Calcular porcentaje
                                        var percentage = totalMuni > 0 ? ((data / totalMuni) * 100).toFixed(1) + '%' : '0%';
                                        var labelText = data + " (" + percentage + ")";

                                        // Posicion: bar._model.x es el final de la barra
                                        ctx.fillText(labelText, bar._model.x + 5, bar._model.y);
                                    });
                                });
                            }
                        },
                        layout: {
                            padding: {
                                right: 60 // Espacio para etiquetas
                            }
                        }
                    }
                });
            }
        }

    } catch (e) { console.error(e); }
}
