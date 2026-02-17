import Chart from 'chart.js/auto';

// Make Chart available globally
window.Chart = Chart;

// Chart.js default styling for dark theme
Chart.defaults.color = '#a1a1aa';
Chart.defaults.borderColor = '#27272a';
Chart.defaults.font.family = "'IBM Plex Mono', monospace";

/**
 * Default chart presets for common use cases
 */
const chartPresets = {
    cost: {
        label: 'Cost ($)',
        color: '#3b82f6',
        bgOpacity: 0.3,
        formatValue: (v) => {
            // Show appropriate precision based on value magnitude
            if (v === 0) return '$0.00';
            if (v < 0.01) return '$' + v.toFixed(4);
            if (v < 1) return '$' + v.toFixed(3);
            return '$' + v.toFixed(2);
        },
        formatTick: (v) => {
            // Dynamic decimal places based on value
            if (v === 0) return '$0';
            if (v < 0.001) return '$' + v.toFixed(4);
            if (v < 0.01) return '$' + v.toFixed(3);
            if (v < 1) return '$' + v.toFixed(2);
            if (v < 100) return '$' + v.toFixed(1);
            return '$' + v.toFixed(0);
        },
    },
    tokens: {
        label: 'Tokens',
        color: '#3b82f6',
        bgOpacity: 0.3,
        formatValue: (v) => v.toLocaleString() + ' tokens',
        formatTick: (v) => {
            if (v >= 1000000) return (v / 1000000).toFixed(1) + 'M';
            if (v >= 1000) return (v / 1000).toFixed(0) + 'k';
            return v;
        },
    },
    requests: {
        label: 'Requests',
        color: '#3b82f6',
        bgOpacity: 0.3,
        formatValue: (v) => v.toLocaleString() + ' requests',
        formatTick: (v) => {
            if (v >= 1000000) return (v / 1000000).toFixed(1) + 'M';
            if (v >= 1000) return (v / 1000).toFixed(0) + 'k';
            return v;
        },
    },
};

/**
 * Create a line chart with gradient fill
 * 
 * @param {HTMLCanvasElement} canvas - The canvas element
 * @param {Object} config - Chart configuration
 * @param {string[]} config.labels - X-axis labels
 * @param {number[]} config.data - Y-axis data points
 * @param {string|Object} config.preset - Preset name ('cost', 'tokens', 'requests') OR custom preset object
 * @param {string} config.preset.label - Dataset label
 * @param {string} config.preset.color - Line color (hex)
 * @param {number} [config.preset.bgOpacity=0.3] - Gradient background opacity
 * @param {Function} [config.preset.formatValue] - Tooltip value formatter
 * @param {Function} [config.preset.formatTick] - Y-axis tick formatter
 * @param {string} [config.type='line'] - Chart type (line, bar, etc.)
 * @returns {Chart} The Chart.js instance
 */
window.createChart = function(canvas, config) {
    if (!canvas || typeof canvas.getContext !== 'function') {
        console.error('Invalid canvas element provided to createChart');
        return null;
    }

    if (!config || typeof config !== 'object') {
        console.error('Invalid chart config provided');
        return null;
    }

    if (!('preset' in config)) {
        console.error('Chart config is missing preset');
        return null;
    }

    const ctx = canvas.getContext('2d');
    if (!ctx) {
        console.error('Unable to get 2d context from canvas');
        return null;
    }
    
    // Resolve preset (string name or custom object)
    const preset = typeof config.preset === 'string' 
        ? chartPresets[config.preset] 
        : config.preset;
    
    if (!preset) {
        console.error('Unknown chart preset:', config.preset);
        return null;
    }
    
    // Default formatters
    const defaultFormatter = (v) => v;
    const formatValue = preset.formatValue || defaultFormatter;
    const formatTick = preset.formatTick || defaultFormatter;
    
    // Create gradient
    const bgOpacity = preset.bgOpacity ?? 0.3;
    const bgGradient = ctx.createLinearGradient(0, 0, 0, canvas.height || 256);
    bgGradient.addColorStop(0, preset.color + Math.round(bgOpacity * 255).toString(16).padStart(2, '0'));
    bgGradient.addColorStop(1, preset.color + '00');
    
    const chartType = config.type || 'line';
    
    return new Chart(ctx, {
        type: chartType,
        data: {
            labels: config.labels,
            datasets: [{
                label: preset.label,
                data: config.data,
                borderColor: preset.color,
                backgroundColor: bgGradient,
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 3,
                pointHoverRadius: 5,
                pointBackgroundColor: preset.color,
                pointBorderColor: '#18181b',
                pointBorderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 250,
                easing: 'easeOutQuart',
            },
            transitions: {
                active: {
                    animation: {
                        duration: 150,
                    },
                },
            },
            interaction: {
                intersect: false,
                mode: 'index',
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#27272a',
                    titleColor: '#fafafa',
                    bodyColor: '#d4d4d8',
                    borderColor: '#3f3f46',
                    borderWidth: 1,
                    padding: 10,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return formatValue(context.parsed.y);
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        color: '#27272a',
                        drawBorder: false,
                    },
                    ticks: {
                        color: '#71717a',
                        font: {
                            size: 11,
                            family: "'IBM Plex Mono', monospace"
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#27272a',
                        drawBorder: false,
                    },
                    ticks: {
                        color: '#71717a',
                        font: {
                            size: 11,
                            family: "'IBM Plex Mono', monospace"
                        },
                        callback: formatTick,
                    }
                }
            }
        }
    });
};

/**
 * Register a new chart preset for reuse
 * 
 * @param {string} name - Preset name
 * @param {Object} preset - Preset configuration
 */
window.registerChartPreset = function(name, preset) {
    chartPresets[name] = preset;
};

/**
 * Alpine.js data component for charts
 * Usage: x-data="chartComponent()" x-init="mount({ labels, data, preset: 'cost' })"
 * 
 * @returns {Object} Alpine component
 */
window.chartComponent = function() {
    return {
        chart: null,
        
        /**
         * Mount the chart.
         * Supports both mount(config) and mount(el, config).
         *
         * @param {Object|HTMLElement} firstArg - Chart config OR wrapper element
         * @param {Object|null} secondArg - Chart config when firstArg is element
         */
        mount(firstArg, secondArg = null) {
            const config = secondArg ?? firstArg;
            const element = secondArg ? firstArg : this.$el;

            this.$nextTick(() => {
                if (!element || typeof element.querySelector !== 'function') {
                    console.error('Chart container not found');
                    return;
                }

                const canvas = this.$refs?.canvas ?? element.querySelector('canvas');
                if (!canvas) {
                    console.error('Canvas not found');
                    return;
                }

                if (!canvas.isConnected) {
                    return;
                }

                const existingChart = Chart.getChart(canvas);
                if (existingChart) {
                    existingChart.destroy();
                }

                if (this.chart) {
                    this.chart.destroy();
                    this.chart = null;
                }

                this.chart = createChart(canvas, config);
            });
        },
        
        /**
         * Destroy the chart instance
         */
        destroy() {
            const canvas = this.$refs?.canvas;
            const existingChart = canvas ? Chart.getChart(canvas) : null;
            if (existingChart) {
                existingChart.destroy();
            }

            if (this.chart) {
                this.chart.destroy();
                this.chart = null;
            }
        }
    };
};

/**
 * Color palette for multi-dataset charts
 */
const modelColors = [
    '#8b5cf6', // Violet
    '#3b82f6', // Blue
    '#10b981', // Emerald
    '#f59e0b', // Amber
    '#ef4444', // Red
];

/**
 * Alpine.js data component for requests chart with multi-dataset support
 * Usage: x-data="requestsChartComponent()" x-init="mount({ labels, totalData, modelData, requestsChartType })"
 * 
 * @returns {Object} Alpine component
 */
window.requestsChartComponent = function() {
    return {
        chart: null,
        
        /**
         * Mount the requests chart.
         *
         * @param {Object} config - Chart configuration
         * @param {string[]} config.labels - X-axis labels
         * @param {number[]} config.totalData - Total requests per date
         * @param {Object} config.modelData - Object with model names as keys and arrays of counts as values
         * @param {string} config.requestsChartType - 'total' or 'per_model'
         */
        mount(config) {
            this.$nextTick(() => {
                const element = this.$el;
                
                if (!element || typeof element.querySelector !== 'function') {
                    console.error('Chart container not found');
                    return;
                }

                const canvas = this.$refs?.canvas ?? element.querySelector('canvas');
                if (!canvas) {
                    console.error('Canvas not found');
                    return;
                }

                if (!canvas.isConnected) {
                    return;
                }

                // Destroy existing chart
                const existingChart = Chart.getChart(canvas);
                if (existingChart) {
                    existingChart.destroy();
                }

                if (this.chart) {
                    this.chart.destroy();
                    this.chart = null;
                }

                // Create the appropriate chart based on requests chart type
                if (config.requestsChartType === 'per_model' && Object.keys(config.modelData).length > 0) {
                    this.chart = this.createPerModelChart(canvas, config);
                } else {
                    this.chart = this.createTotalChart(canvas, config);
                }
            });
        },

        /**
         * Create a line chart for total requests
         */
        createTotalChart(canvas, config) {
            return createChart(canvas, {
                labels: config.labels,
                data: config.totalData,
                preset: 'requests'
            });
        },

        /**
         * Create a multi-dataset line chart for requests per model
         */
        createPerModelChart(canvas, config) {
            const ctx = canvas.getContext('2d');
            
            const datasets = Object.entries(config.modelData).map(([model, data], index) => {
                const color = modelColors[index % modelColors.length];
                const bgGradient = ctx.createLinearGradient(0, 0, 0, canvas.height || 256);
                bgGradient.addColorStop(0, color + '4D'); // 30% opacity
                bgGradient.addColorStop(1, color + '00'); // 0% opacity

                return {
                    label: model,
                    data: data,
                    borderColor: color,
                    backgroundColor: bgGradient,
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    pointBackgroundColor: color,
                    pointBorderColor: '#18181b',
                    pointBorderWidth: 2,
                };
            });

            return new Chart(ctx, {
                type: 'line',
                data: {
                    labels: config.labels,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 250,
                        easing: 'easeOutQuart',
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            align: 'end',
                            labels: {
                                color: '#a1a1aa',
                                font: {
                                    size: 11,
                                    family: "'IBM Plex Mono', monospace"
                                },
                                usePointStyle: true,
                                pointStyle: 'circle',
                                padding: 15,
                                boxWidth: 8,
                            }
                        },
                        tooltip: {
                            backgroundColor: '#27272a',
                            titleColor: '#fafafa',
                            bodyColor: '#d4d4d8',
                            borderColor: '#3f3f46',
                            borderWidth: 1,
                            padding: 10,
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.parsed.y.toLocaleString() + ' requests';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                color: '#27272a',
                                drawBorder: false,
                            },
                            ticks: {
                                color: '#71717a',
                                font: {
                                    size: 11,
                                    family: "'IBM Plex Mono', monospace"
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#27272a',
                                drawBorder: false,
                            },
                            ticks: {
                                color: '#71717a',
                                font: {
                                    size: 11,
                                    family: "'IBM Plex Mono', monospace"
                                },
                                callback: function(value) {
                                    if (value >= 1000000) return (value / 1000000).toFixed(1) + 'M';
                                    if (value >= 1000) return (value / 1000).toFixed(0) + 'k';
                                    return value;
                                }
                            }
                        }
                    }
                }
            });
        },
        
        /**
         * Destroy the chart instance
         */
        destroy() {
            const canvas = this.$refs?.canvas;
            const existingChart = canvas ? Chart.getChart(canvas) : null;
            if (existingChart) {
                existingChart.destroy();
            }

            if (this.chart) {
                this.chart.destroy();
                this.chart = null;
            }
        }
    };
};
