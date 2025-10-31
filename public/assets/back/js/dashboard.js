/**
 * Enhanced E-Commerce Dashboard JavaScript
 * Author: GitHub Copilot
 * Features: Tab Management, Calendar Widget, Date Filtering, Charts, Clock
 */

class DashboardManager {
    constructor() {
        this.currentTab = 'today'; // Default to 'today' as that's active in the HTML
        this.currentDate = new Date();
        this.selectedDate = null;
        this.dateRange = { start: null, end: null };
        this.charts = {};
        this.clockInterval = null;
        this.calendarData = {};
        
        // Initialize with error handling
        try {
            this.init();
        } catch (error) {
            console.error('Dashboard initialization failed:', error);
            this.showNotification('Dashboard initialization failed. Some features may not work.', 'error');
        }
    }

    init() {
        this.initializeEventListeners();
        this.initializeCalendar();
        this.initializeClock();
        this.initializeCharts();
        
        // Find the currently active tab in the DOM first
        const activeTab = document.querySelector('.dashboard-tab-btn.active, .nav-link.active[data-period]');
        if (activeTab && activeTab.dataset.period) {
            this.currentTab = activeTab.dataset.period;
            console.log('Found active tab:', this.currentTab);
        } else {
            // Default to 'today' if no active tab found
            this.currentTab = 'today';
            console.log('No active tab found, defaulting to:', this.currentTab);
        }
        
        // Restore last selected period from localStorage (but prioritize DOM state)
        const savedPeriod = localStorage.getItem('dashboard_period');
        if (savedPeriod && savedPeriod !== 'custom' && !activeTab) {
            this.currentTab = savedPeriod;
            // Update tab UI
            document.querySelectorAll('.dashboard-tab-btn').forEach(tab => {
                tab.classList.remove('active');
            });
            const savedTab = document.querySelector(`.dashboard-tab-btn[data-period="${savedPeriod}"]`);
            if (savedTab) {
                savedTab.classList.add('active');
            }
        }
        
        // Load initial dashboard data
        this.loadDashboardData();
        
        console.log('Dashboard initialized with period:', this.currentTab);
    }

    initializeEventListeners() {
        // Tab management - Updated for new tab system
        document.querySelectorAll('.dashboard-tab-btn').forEach(tab => {
            tab.addEventListener('click', (e) => {
                e.preventDefault();
                this.handleTabSwitch(e.target.dataset.period);
            });
        });

        // Fallback for old nav-link system
        document.querySelectorAll('.nav-link[data-period]').forEach(tab => {
            tab.addEventListener('click', (e) => {
                e.preventDefault();
                this.handleTabSwitch(e.target.dataset.period);
            });
        });

        // Date range functionality
        const startDateInput = document.getElementById('start-date');
        const endDateInput = document.getElementById('end-date');
        
        if (startDateInput && endDateInput) {
            startDateInput.addEventListener('change', () => this.handleDateRangeChange());
            endDateInput.addEventListener('change', () => this.handleDateRangeChange());
        }

        // Quick select buttons
        document.querySelectorAll('.quick-select-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const days = e.target.getAttribute('data-days');
                if (days) {
                    this.handleQuickSelect(parseInt(days));
                }
            });
        });

        // Apply date range button
        const applyBtn = document.getElementById('apply-date-range');
        if (applyBtn) {
            applyBtn.addEventListener('click', () => this.applyDateRange());
        }

        // Reset date range button  
        const resetBtn = document.getElementById('reset-date-range');
        if (resetBtn) {
            resetBtn.addEventListener('click', () => this.resetDateRange());
        }

        // Calendar navigation
        document.addEventListener('click', (e) => {
            if (e.target.matches('.calendar-nav-btn[data-action="prev"]')) {
                this.navigateCalendar(-1);
            } else if (e.target.matches('.calendar-nav-btn[data-action="next"]')) {
                this.navigateCalendar(1);
            } else if (e.target.matches('.calendar-day:not(.other-month)')) {
                this.selectCalendarDate(e.target);
            }
        });

        // Calendar control buttons
        document.querySelectorAll('.calendar-control-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.handleCalendarControl(e.target.dataset.action);
            });
        });
    }

    handleTabSwitch(period) {
        console.log('Switching to period:', period); // Debug log
        
        // Update current tab state immediately
        this.currentTab = period;
        
        // Save to localStorage for persistence
        localStorage.setItem('dashboard_period', period);
        
        // Update active tab for new dashboard tab system
        document.querySelectorAll('.dashboard-tab-btn').forEach(tab => {
            tab.classList.remove('active');
        });
        
        const newActiveTab = document.querySelector(`.dashboard-tab-btn[data-period="${period}"]`);
        if (newActiveTab) {
            newActiveTab.classList.add('active');
        }
        
        // Fallback: Update active tab for old nav-link system
        document.querySelectorAll('.nav-link[data-period]').forEach(tab => {
            tab.classList.remove('active');
        });
        
        const oldActiveTab = document.querySelector(`.nav-link[data-period="${period}"]`);
        if (oldActiveTab) {
            oldActiveTab.classList.add('active');
        }

        // Handle custom range visibility
        const dateRangeContainer = document.getElementById('date-range-container');
        if (dateRangeContainer) {
            if (period === 'custom') {
                // Show with animation
                dateRangeContainer.style.display = 'block';
                dateRangeContainer.classList.add('show');
                this.initializeDateInputs();
                // Don't load data yet for custom - wait for apply button
            } else {
                // Hide with animation
                dateRangeContainer.classList.remove('show');
                setTimeout(() => {
                    dateRangeContainer.style.display = 'none';
                }, 300);
                // Load data immediately for predefined periods
                this.loadDashboardData(period);
            }
        } else {
            // If no date range container, always load data
            this.loadDashboardData(period);
        }

        // Add animation if tab-content exists
        const tabContent = document.querySelector('.tab-content');
        if (tabContent) {
            tabContent.classList.add('tab-fade-in');
            setTimeout(() => {
                tabContent.classList.remove('tab-fade-in');
            }, 300);
        }

        // Store current period in localStorage for persistence
        localStorage.setItem('dashboard_period', period);
    }

    initializeDateInputs() {
        const today = new Date();
        const startDate = new Date(today.getFullYear(), today.getMonth(), 1);
        
        document.getElementById('start-date').value = this.formatDateForInput(startDate);
        document.getElementById('end-date').value = this.formatDateForInput(today);
    }

    handleDateRangeChange() {
        const startDate = document.getElementById('start-date').value;
        const endDate = document.getElementById('end-date').value;
        
        if (startDate && endDate) {
            this.dateRange = {
                start: new Date(startDate),
                end: new Date(endDate)
            };
        }
    }

    handleQuickSelect(days) {
        const today = new Date();
        const startDate = new Date(today.getTime() - (days * 24 * 60 * 60 * 1000));
        
        const startInput = document.getElementById('start-date');
        const endInput = document.getElementById('end-date');
        
        if (startInput && endInput) {
            startInput.value = this.formatDateForInput(startDate);
            endInput.value = this.formatDateForInput(today);
            
            this.dateRange = { 
                start: startDate, 
                end: today 
            };
        }
        
        // Update active button
        document.querySelectorAll('.quick-select-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        
        // Find and highlight the clicked button
        const clickedBtn = document.querySelector(`[data-days="${days}"]`);
        if (clickedBtn) {
            clickedBtn.classList.add('active');
        }
    }

    applyDateRange() {
        console.log('🔧 applyDateRange called');
        
        const startDateInput = document.getElementById('start-date');
        const endDateInput = document.getElementById('end-date');
        
        if (!startDateInput || !endDateInput) {
            console.error('❌ Date input elements not found');
            this.showNotification('Date input fields not found', 'error');
            return;
        }
        
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;
        
        console.log('📅 Selected dates:', { startDate, endDate });
        
        if (startDate && endDate) {
            // Validate date range
            const start = new Date(startDate);
            const end = new Date(endDate);
            
            console.log('📅 Parsed dates:', { start, end });
            
            if (start > end) {
                console.warn('⚠️ Invalid date range: start after end');
                this.showNotification('Start date cannot be after end date', 'error');
                return;
            }
            
            // Check if range is too large (more than 5 years)
            const daysDifference = (end - start) / (1000 * 60 * 60 * 24);
            console.log('📊 Days difference:', daysDifference);
            
            // Calculate years for better user feedback
            const yearsDifference = daysDifference / 365.25;
            console.log('📅 Years difference:', yearsDifference.toFixed(1));
            
            if (daysDifference > 1825) { // 5 years
                console.warn('⚠️ Date range too large:', daysDifference, 'days (', yearsDifference.toFixed(1), 'years)');
                this.showNotification(`Date range too large: ${yearsDifference.toFixed(1)} years. Maximum allowed is 5 years.`, 'warning');
                return;
            }
            
            // Show warning for very large ranges that might be slow
            if (daysDifference > 1095) { // 3 years
                console.log('⚠️ Large date range warning:', daysDifference, 'days');
                this.showNotification(`Large date range (${yearsDifference.toFixed(1)} years) - loading may take longer`, 'info');
            }
            
            // Set the date range
            this.dateRange = {
                start: start,
                end: end
            };
            
            console.log('✅ Date range set:', this.dateRange);
            
            // Update current tab
            this.currentTab = 'custom';
            
            // Update UI to show custom tab as active
            document.querySelectorAll('.dashboard-tab-btn, .nav-link[data-period]').forEach(tab => {
                tab.classList.remove('active');
            });
            
            const customTab = document.querySelector('[data-period="custom"]');
            if (customTab) {
                customTab.classList.add('active');
                console.log('🎯 Custom tab marked as active');
            }
            
            // Load dashboard data with custom range
            console.log('🔄 Loading dashboard data for custom range...');
            this.loadDashboardData('custom');
            
            // Show success notification
            const formatOptions = { year: 'numeric', month: 'short', day: 'numeric' };
            const startFormatted = start.toLocaleDateString('en-US', formatOptions);
            const endFormatted = end.toLocaleDateString('en-US', formatOptions);
            this.showNotification(`Showing data from ${startFormatted} to ${endFormatted}`, 'success');
            
            // Hide the date range container with animation
            const dateRangeContainer = document.getElementById('date-range-container');
            if (dateRangeContainer) {
                dateRangeContainer.classList.remove('show');
                setTimeout(() => {
                    dateRangeContainer.style.display = 'none';
                }, 300);
                console.log('📦 Date range container hidden');
            }
        } else {
            console.warn('⚠️ Missing date values');
            this.showNotification('Please select both start and end dates', 'error');
        }
    }

    resetDateRange() {
        // Reset to default date range (last 7 days)
        const today = new Date();
        const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
        
        const startInput = document.getElementById('start-date');
        const endInput = document.getElementById('end-date');
        
        if (startInput && endInput) {
            startInput.value = this.formatDateForInput(weekAgo);
            endInput.value = this.formatDateForInput(today);
        }
        
        // Clear active quick select buttons
        document.querySelectorAll('.quick-select-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        
        // Reset date range
        this.dateRange = { start: weekAgo, end: today };
        
        this.showNotification('Date range reset to last 7 days', 'info');
    }

    formatDateForInput(date) {
        return date.toISOString().split('T')[0];
    }

    async loadDashboardData(period = null) {
        this.showLoadingState();
        
        // Ensure we have a valid period - default to 'today' if nothing is set
        const currentPeriod = period || this.currentTab || 'today';
        console.log('Loading dashboard data for period:', currentPeriod);
        console.log('Received period param:', period);
        console.log('Current tab state:', this.currentTab);
        
        try {
            // Update the statistics cards based on period
            this.updateStatisticsForPeriod(currentPeriod);
            
            // Update chart titles and data based on period
            this.updateChartTitles(currentPeriod);
            
            // Build API parameters
            const params = new URLSearchParams({
                period: currentPeriod
            });

            // Add custom date range if applicable
            if (currentPeriod === 'custom' && this.dateRange.start && this.dateRange.end) {
                const startDateFormatted = this.formatDateForInput(this.dateRange.start);
                const endDateFormatted = this.formatDateForInput(this.dateRange.end);
                
                params.append('start_date', startDateFormatted);
                params.append('end_date', endDateFormatted);
                
                console.log('🗓️ Custom date range added to API params:');
                console.log('  Start Date:', startDateFormatted);
                console.log('  End Date:', endDateFormatted);
                console.log('  Date Range Object:', this.dateRange);
            } else if (currentPeriod === 'custom') {
                console.warn('⚠️ Custom period selected but no date range set');
                this.showNotification('Please select a date range first', 'warning');
                this.hideLoadingState();
                return;
            }

            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            const headers = {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            };
            
            if (csrfToken) {
                headers['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
            }

            console.log('Making API request to:', `/admin/dashboard/data?${params.toString()}`);
            console.log('Headers:', headers);

            // Fetch data from Laravel backend
            const response = await fetch(`/admin/dashboard/data`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    period: currentPeriod,
                    start_date: this.customDateRange.start,
                    end_date: this.customDateRange.end,
                    _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                })
            });

            console.log('Response status:', response.status);
            console.log('Response ok:', response.ok);

            if (!response.ok) {
                // Get detailed error information
                let errorMessage = `HTTP error! status: ${response.status}`;
                try {
                    const errorData = await response.json();
                    if (errorData.message) {
                        errorMessage += ` - ${errorData.message}`;
                    }
                    if (errorData.error && window.location.hostname === 'localhost') {
                        console.error('Detailed error:', errorData.error);
                    }
                } catch (e) {
                    // If can't parse JSON, get text response
                    try {
                        const errorText = await response.text();
                        console.error('Error response:', errorText.substring(0, 500));
                    } catch (te) {
                        console.error('Failed to get error details');
                    }
                }
                
                // If authentication error, try to fallback to test API
                if (response.status === 401 || response.status === 403) {
                    console.warn('Authentication failed, trying fallback API...');
                    const fallbackResponse = await fetch(`/api-test-dashboard.php?period=${currentPeriod}`);
                    if (fallbackResponse.ok) {
                        const fallbackData = await fallbackResponse.json();
                        console.log('Fallback API data received:', fallbackData);
                        this.updateDashboardCards(fallbackData);
                        this.showNotification('Dashboard updated using fallback API', 'warning');
                        return;
                    }
                }
                
                throw new Error(errorMessage);
            }

            const data = await response.json();
            console.log('Dashboard data received:', data);
            
            // Update dashboard with new data
            this.updateDashboardCards(data);
            this.updateCharts(data);
            this.updateRecentOrders(data.recentOrders);
            
            // Show success notification
            this.showNotification(`Dashboard updated for ${this.getPeriodLabel(currentPeriod)}`, 'success');
            
        } catch (error) {
            console.error('Error loading dashboard data:', error);
            
            // Show user-friendly error message
            let userMessage = 'Error loading dashboard data';
            if (error.message.includes('500')) {
                userMessage = 'Server error while loading dashboard data. Please try again.';
            } else if (error.message.includes('403')) {
                userMessage = 'You do not have permission to access this data.';
            } else if (error.message.includes('404')) {
                userMessage = 'Dashboard endpoint not found.';
            }
            
            this.showNotification(userMessage, 'error');
            
            // Try to update UI with basic fallback
            try {
                this.updateStatisticsForPeriod(currentPeriod);
                this.updateChartTitles(currentPeriod);
                this.showNotification('Using cached data due to server error', 'warning');
            } catch (fallbackError) {
                console.error('Fallback update failed:', fallbackError);
            }
        } finally {
            this.hideLoadingState();
        }
    }

    updateStatisticsForPeriod(period) {
        // Update the earning period label
        const earningPeriodSpan = document.getElementById('earning-period');
        if (earningPeriodSpan) {
            const periodLabels = {
                'all': 'All Time Revenue',
                'today': 'Today Revenue',
                'week': 'This Week Revenue', 
                'month': 'This Month Revenue',
                'year': 'This Year Revenue',
                'custom': 'Custom Range Revenue'
            };
            earningPeriodSpan.textContent = periodLabels[period] || 'All Time Revenue';
        }
        
        // Add visual feedback
        document.querySelectorAll('.hover-card').forEach(card => {
            card.classList.add('updating');
            setTimeout(() => {
                card.classList.remove('updating');
                card.classList.add('updated');
                setTimeout(() => card.classList.remove('updated'), 600);
            }, 500);
        });
    }

    updateChartTitles(period) {
        const periodLabels = {
            'all': 'All Time',
            'today': 'Today', 
            'week': 'This Week',
            'month': 'This Month',
            'year': 'This Year',
            'custom': 'Custom Range'
        };
        
        const periodLabel = periodLabels[period] || 'Today';
        
        // Update chart titles
        const chartTitles = [
            { id: 'orders-chart-title', text: `Orders - ${periodLabel}` },
            { id: 'revenue-chart-title', text: `Revenue - ${periodLabel}` },
            { id: 'categories-chart-title', text: `Sales - ${periodLabel}` },
            { id: 'products-chart-title', text: `Customers - ${periodLabel}` }
        ];
        
        chartTitles.forEach(({id, text}) => {
            const element = document.getElementById(id);
            if (element) {
                // Find the text content after the icon
                const textNode = element.lastChild;
                if (textNode && textNode.nodeType === Node.TEXT_NODE) {
                    textNode.textContent = text;
                } else {
                    // If no text node, find the text after the icon
                    const iconElement = element.querySelector('i');
                    if (iconElement && iconElement.nextSibling) {
                        iconElement.nextSibling.textContent = text;
                    } else {
                        element.innerHTML = element.innerHTML.replace(/\s*-.*$/, '') + ` - ${text.split(' - ')[1]}`;
                    }
                }
            }
        });

        // Update card subtitles for period-specific data
        const cardSubtitles = [
            { id: 'total-customers-subtitle', text: period === 'all' ? 'Total Customers' : `New Customers - ${periodLabel}` },
            { id: 'total-reviews-subtitle', text: period === 'all' ? 'Total Reviews' : `Reviews - ${periodLabel}` },
            { id: 'total-subscribers-subtitle', text: period === 'all' ? 'Newsletter Subscribers' : `New Subscribers - ${periodLabel}` }
        ];

        cardSubtitles.forEach(({id, text}) => {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = text;
            } else {
                // Fallback: find by class or other selector
                const parentCard = document.querySelector(`#${id.replace('-subtitle', '')}`);
                if (parentCard) {
                    const subtitle = parentCard.parentElement.querySelector('.card-subtitle, .text-muted, small');
                    if (subtitle) {
                        subtitle.textContent = text;
                    }
                }
            }
        });
    }

    updateDashboardCards(data) {
        // Debug log to see what data we're receiving
        console.log('updateDashboardCards called with data:', data);
        
        // Update main statistics cards with animation
        const updates = {
            'total-orders': data.totalOrders || 0,
            'pending-orders': data.totalPendingOrders || 0,
            'delivered-orders': data.totalDeliveredOrders || 0,
            'canceled-orders': data.totalCanceledOrders || 0,
            'total-customers': data.totalCustomers || 0,
            'total-products': data.totalProducts || 0,
            'total-categories': data.totalCategories || 0,
            'total-brands': data.totalBrands || 0,
            'total-earning': data.totalEarning || 0,
            'product-sales': data.totalSales || 0,
            'total-reviews': data.totalReviews || 0,
            'total-subscribers': data.totalSubscribers || 0
        };

        console.log('Card updates to apply:', updates);
        
        // Special logging for the newly dynamic cards
        console.log('🔥 DYNAMIC CARDS NOW UPDATING:');
        console.log(`  📦 Products: ${updates['total-products']} (was static, now dynamic!)`);
        console.log(`  📂 Categories: ${updates['total-categories']} (was static, now dynamic!)`);
        console.log(`  🏷️ Brands: ${updates['total-brands']} (was static, now dynamic!)`);

        // Animate each statistic update
        Object.entries(updates).forEach(([id, value]) => {
            const element = document.getElementById(id);
            if (element) {
                const currentValue = parseInt(element.textContent.replace(/[^0-9]/g, '')) || 0;
                const newValue = parseInt(value) || 0;
                
                // Extra logging for the dynamic cards we just fixed
                if (['total-products', 'total-categories', 'total-brands'].includes(id)) {
                    console.log(`✨ UPDATING DYNAMIC CARD ${id}: ${currentValue} -> ${newValue}`);
                } else {
                    console.log(`Updating ${id}: ${currentValue} -> ${newValue}`);
                }
                
                // Add updating class for visual feedback
                element.parentElement.parentElement.parentElement.classList.add('updating');
                
                // Animate the number change
                this.animateValue(element, currentValue, newValue, 1000);
                
                // Remove updating class after animation
                setTimeout(() => {
                    element.parentElement.parentElement.parentElement.classList.remove('updating');
                    element.parentElement.parentElement.parentElement.classList.add('updated');
                    setTimeout(() => {
                        element.parentElement.parentElement.parentElement.classList.remove('updated');
                    }, 600);
                }, 1000);
            } else {
                console.warn(`Element with ID '${id}' not found`);
            }
        });

        // Update earning period label if needed
        const earningPeriodSpan = document.getElementById('earning-period');
        if (earningPeriodSpan && data.period) {
            const periodLabels = {
                'all': 'All Time Revenue',
                'today': 'Today Revenue',
                'week': 'This Week Revenue', 
                'month': 'This Month Revenue',
                'year': 'This Year Revenue',
                'custom': 'Custom Range Revenue'
            };
            earningPeriodSpan.textContent = periodLabels[data.period] || 'All Time Revenue';
        }

        // Update recent activity if present
        if (data.recentOrders) {
            this.updateRecentOrders(data.recentOrders);
        }

        console.log('Dashboard cards updated successfully');
    }

    animateValue(element, start, end, duration) {
        const startTime = Date.now();
        const range = end - start;
        const isEarning = element.id === 'total-earning';

        const step = () => {
            const elapsed = Date.now() - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const current = Math.floor(start + range * progress);
            
            // Format based on element type
            if (isEarning) {
                // For earning, preserve currency symbol and format
                const originalText = element.textContent;
                const currencySymbol = originalText.match(/[^0-9.,]/g)?.[0] || '$';
                element.textContent = currencySymbol + this.formatNumber(current);
            } else {
                // For regular numbers
                element.textContent = this.formatNumber(current);
            }
            
            if (progress < 1) {
                requestAnimationFrame(step);
            }
        };

        step();
    }

    formatNumber(num) {
        if (num >= 1000000) {
            return (num / 1000000).toFixed(1) + 'M';
        } else if (num >= 1000) {
            return (num / 1000).toFixed(1) + 'K';
        }
        return num.toLocaleString();
    }

    getPeriodLabel(period) {
        const labels = {
            'all': 'All Time',
            'today': 'Today',
            'week': 'This Week',
            'month': 'This Month', 
            'year': 'This Year',
            'custom': 'Custom Range'
        };
        return labels[period] || 'Today';
    }

    updateRecentOrders(orders) {
        const container = document.querySelector('.table-responsive tbody, #recent-orders-table tbody');
        if (!container || !orders) {
            console.log('Recent orders container not found or no orders data');
            return;
        }

        // Add loading animation to the recent orders section
        const ordersCard = container.closest('.card');
        if (ordersCard) {
            ordersCard.classList.add('updating');
        }

        // Clear existing orders and add new ones
        container.innerHTML = orders.map(order => `
            <tr class="order-row-animation">
                <td class="text-white">
                    #${order.transaction_number || order.order_number || order.id}
                </td>
                <td class="text-white">
                    ${order.customer_name || (order.shipping_info ? JSON.parse(order.shipping_info).ship_first_name : '') || 'N/A'}
                </td>
                <td>
                    ${this.getOrderStatusBadge(order.order_status || order.status)}
                </td>
                <td class="text-white fw-medium">
                    ৳${parseFloat(order.total || order.total_amount || order.amount || 0).toFixed(2)}
                </td>
                <td class="text-white">
                    ${new Date(order.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}
                </td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <a href="/admin/order/invoice/${order.id}" class="btn btn-sm btn-light" title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="/admin/order/edit/${order.id}" class="btn btn-sm btn-warning" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                </td>
            </tr>
        `).join('');

        // Remove updating class after animation
        setTimeout(() => {
            if (ordersCard) {
                ordersCard.classList.remove('updating');
                ordersCard.classList.add('updated');
                setTimeout(() => ordersCard.classList.remove('updated'), 600);
            }
        }, 500);

        console.log('Recent orders updated with', orders.length, 'orders');
    }

    getOrderStatusBadge(status) {
        const statusClasses = {
            'Pending': 'bg-warning',
            'Processing': 'bg-info', 
            'Delivered': 'bg-success',
            'Completed': 'bg-success',
            'Cancelled': 'bg-danger',
            'Canceled': 'bg-danger',
            'Refunded': 'bg-secondary'
        };
        
        const badgeClass = statusClasses[status] || 'bg-info';
        return `<span class="badge ${badgeClass}">${status || 'Unknown'}</span>`;
    }

    showNotification(message, type = 'info') {
        // Remove existing notifications
        document.querySelectorAll('.dashboard-notification').forEach(notif => {
            notif.remove();
        });

        const notification = document.createElement('div');
        notification.className = `dashboard-notification alert alert-${this.getAlertType(type)} alert-dismissible fade show`;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        `;
        
        const icons = {
            'success': '✅',
            'error': '❌',
            'warning': '⚠️',
            'info': 'ℹ️'
        };
        
        notification.innerHTML = `
            <div class="d-flex align-items-center">
                <span class="me-2">${icons[type] || icons.info}</span>
                <span>${message}</span>
                <button type="button" class="btn-close ms-auto" aria-label="Close"></button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.classList.remove('show');
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 150);
            }
        }, 5000);

        // Manual dismiss on close button click
        const closeButton = notification.querySelector('.btn-close');
        if (closeButton) {
            closeButton.addEventListener('click', () => {
                notification.classList.remove('show');
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 150);
            });
        }
    }

    getAlertType(type) {
        const mapping = {
            'success': 'success',
            'error': 'danger',
            'warning': 'warning',
            'info': 'info'
        };
        return mapping[type] || 'info';
    }

    showLoadingState() {
        // Add loading class to all statistic cards
        document.querySelectorAll('.hover-card').forEach(card => {
            card.classList.add('updating');
            
            // Add loading spinner to card if it doesn't exist
            if (!card.querySelector('.loading-spinner')) {
                const spinner = document.createElement('div');
                spinner.className = 'loading-spinner';
                spinner.innerHTML = '<div class="spinner-border spinner-border-sm text-light" role="status"><span class="visually-hidden">Loading...</span></div>';
                spinner.style.cssText = 'position: absolute; top: 10px; right: 10px; z-index: 10;';
                card.style.position = 'relative';
                card.appendChild(spinner);
            }
        });

        // Show loading for charts
        document.querySelectorAll('canvas').forEach(canvas => {
            const parent = canvas.parentElement;
            parent.classList.add('chart-loading');
            
            if (!parent.querySelector('.chart-loading-overlay')) {
                const overlay = document.createElement('div');
                overlay.className = 'chart-loading-overlay';
                overlay.innerHTML = '<div class="spinner-border text-light" role="status"></div>';
                overlay.style.cssText = `
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(0,0,0,0.3);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 10;
                `;
                parent.style.position = 'relative';
                parent.appendChild(overlay);
            }
        });

        // Disable tab buttons during loading
        document.querySelectorAll('.dashboard-tab-btn').forEach(btn => {
            btn.disabled = true;
            btn.style.opacity = '0.6';
        });

        console.log('Loading state activated');
    }

    hideLoadingState() {
        // Remove loading class and spinners from statistic cards
        document.querySelectorAll('.hover-card').forEach(card => {
            card.classList.remove('updating');
            card.classList.add('updated');
            
            const spinner = card.querySelector('.loading-spinner');
            if (spinner) {
                spinner.remove();
            }
            
            // Remove updated class after animation
            setTimeout(() => {
                card.classList.remove('updated');
            }, 600);
        });

        // Hide loading for charts
        document.querySelectorAll('.chart-loading-overlay').forEach(overlay => {
            overlay.remove();
        });
        
        document.querySelectorAll('canvas').forEach(canvas => {
            canvas.parentElement.classList.remove('chart-loading');
        });

        // Re-enable tab buttons
        document.querySelectorAll('.dashboard-tab-btn').forEach(btn => {
            btn.disabled = false;
            btn.style.opacity = '1';
        });

        console.log('Loading state deactivated');
    }

    // Calendar Management
    initializeCalendar() {
        // Check if calendar widget exists
        const calendarWidget = document.getElementById('calendar-widget');
        if (!calendarWidget) {
            console.log('Calendar widget not found, skipping calendar initialization');
            return;
        }
        
        // Calendar state variables
        this.currentCalendarMonth = new Date().getMonth();
        this.currentCalendarYear = new Date().getFullYear();
        this.selectedDate = null;
        this.calendarEvents = {};
        
        // Initialize some sample events
        this.initializeCalendarEvents();
        
        // Generate the calendar
        this.generateCalendar();
        
        // Set up global functions for calendar navigation
        window.changeMonth = (direction) => {
            this.currentCalendarMonth += direction;
            if (this.currentCalendarMonth > 11) {
                this.currentCalendarMonth = 0;
                this.currentCalendarYear++;
            } else if (this.currentCalendarMonth < 0) {
                this.currentCalendarMonth = 11;
                this.currentCalendarYear--;
            }
            this.generateCalendar(this.currentCalendarMonth, this.currentCalendarYear);
        };
        
        window.selectDate = (year, month, day) => {
            this.selectDate(year, month, day);
        };
        
        window.goToToday = () => {
            this.goToToday();
        };
        
        window.addEvent = () => {
            this.addEvent();
        };
        
        window.viewCalendarMode = () => {
            this.viewCalendarMode();
        };
    }

    // Enhanced Calendar Generation with Full Functionality
    generateCalendar(month = this.currentCalendarMonth, year = this.currentCalendarYear) {
        const today = new Date();
        const isCurrentMonth = (month === today.getMonth() && year === today.getFullYear());
        const todayDate = today.getDate();

        const monthNames = ["January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"];

        const daysOfWeek = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

        // First day of month and number of days
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const daysInPrevMonth = new Date(year, month, 0).getDate();

        let calendarHTML = `
            <div class="calendar-header">
                <button class="btn btn-sm calendar-nav-btn" onclick="changeMonth(-1)" title="Previous Month">
                    &#8249;
                </button>
                <div class="calendar-month-year">
                    <h6 class="mb-0 calendar-title">${monthNames[month]} ${year}</h6>
                    <small class="calendar-subtitle">${this.getDaysInMonth(month, year)} days</small>
                </div>
                <button class="btn btn-sm calendar-nav-btn" onclick="changeMonth(1)" title="Next Month">
                    &#8250;
                </button>
            </div>
            <div class="calendar-controls">
                <button class="btn btn-xs calendar-control-btn" onclick="goToToday()" title="Go to Today">
                    📅 Today
                </button>
                <button class="btn btn-xs calendar-control-btn" onclick="addEvent()" title="Add Event">
                    ➕ Event
                </button>
                <button class="btn btn-xs calendar-control-btn" onclick="viewCalendarMode()" title="Change View">
                    🗂️ View
                </button>
            </div>
            <div class="calendar-grid">
        `;

        // Add day headers with enhanced styling
        daysOfWeek.forEach((day, index) => {
            const isWeekendHeader = (index === 5 || index === 6); // Friday and Saturday
            const weekendClass = isWeekendHeader ? 'weekend-header' : '';
            calendarHTML += `<div class="calendar-day-header ${weekendClass}">${day}</div>`;
        });

        // Add empty cells for previous month
        for (let i = firstDay - 1; i >= 0; i--) {
            const prevDate = daysInPrevMonth - i;
            const dateKey = `${year}-${month-1}-${prevDate}`;
            const hasEvent = this.calendarEvents[dateKey] ? 'has-event' : '';
            calendarHTML += `
                <div class="calendar-day other-month ${hasEvent}"
                     onclick="selectDate(${year}, ${month-1}, ${prevDate})"
                     data-date="${dateKey}">
                    ${prevDate}
                    ${this.calendarEvents[dateKey] ? '<div class="event-indicator"></div>' : ''}
                </div>`;
        }

        // Add days of current month
        for (let day = 1; day <= daysInMonth; day++) {
            const isToday = (isCurrentMonth && day === todayDate) ? 'today' : '';
            const isSelected = (this.selectedDate && this.selectedDate.getDate() === day &&
                              this.selectedDate.getMonth() === month &&
                              this.selectedDate.getFullYear() === year) ? 'selected' : '';
            const dateKey = `${year}-${month}-${day}`;
            const hasEvent = this.calendarEvents[dateKey] ? 'has-event' : '';
            const isWeekend = new Date(year, month, day).getDay() === 5 || new Date(year, month, day).getDay() === 6 ? 'weekend' : '';

            calendarHTML += `
                <div class="calendar-day ${isToday} ${isSelected} ${hasEvent} ${isWeekend}"
                     onclick="selectDate(${year}, ${month}, ${day})"
                     data-date="${dateKey}"
                     title="${this.getDateTitle(year, month, day)}">
                    ${day}
                    ${this.calendarEvents[dateKey] ? '<div class="event-indicator"></div>' : ''}
                    ${isToday ? '<div class="today-indicator"></div>' : ''}
                </div>`;
        }

        // Add empty cells for next month
        const remainingCells = 42 - (firstDay + daysInMonth);
        for (let day = 1; day <= remainingCells && remainingCells < 7; day++) {
            const dateKey = `${year}-${month+1}-${day}`;
            const hasEvent = this.calendarEvents[dateKey] ? 'has-event' : '';
            calendarHTML += `
                <div class="calendar-day other-month ${hasEvent}"
                     onclick="selectDate(${year}, ${month+1}, ${day})"
                     data-date="${dateKey}">
                    ${day}
                    ${this.calendarEvents[dateKey] ? '<div class="event-indicator"></div>' : ''}
                </div>`;
        }

        calendarHTML += '</div>';

        // Add selected date info
        calendarHTML += `
            <div class="calendar-footer">
                <div id="selected-date-info" class="selected-date-info">
                    ${this.selectedDate ? this.formatSelectedDate(this.selectedDate) : 'Click a date to select'}
                </div>
            </div>
        `;

        const calendarElement = document.getElementById('calendar-widget');
        if (calendarElement) {
            calendarElement.innerHTML = calendarHTML;
            // Add animation class
            calendarElement.classList.add('calendar-updated');
            setTimeout(() => {
                calendarElement.classList.remove('calendar-updated');
            }, 300);
        }
    }

    // Helper functions for calendar
    getDaysInMonth(month, year) {
        return new Date(year, month + 1, 0).getDate();
    }

    getDateTitle(year, month, day) {
        const date = new Date(year, month, day);
        const dayNames = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
        const monthNames = ["January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"];
        return `${dayNames[date.getDay()]}, ${monthNames[month]} ${day}, ${year}`;
    }

    formatSelectedDate(date) {
        const options = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        };
        return `Selected: ${date.toLocaleDateString('en-US', options)}`;
    }

    // Calendar interaction functions
    selectDate(year, month, day) {
        this.selectedDate = new Date(year, month, day);
        const dateKey = `${year}-${month}-${day}`;

        // Update calendar display
        this.generateCalendar(this.currentCalendarMonth, this.currentCalendarYear);

        // Show date details or trigger events
        this.showDateDetails(this.selectedDate, dateKey);

        // Add visual feedback
        const selectedElement = document.querySelector(`[data-date="${dateKey}"]`);
        if (selectedElement) {
            selectedElement.classList.add('date-selected-animation');
            setTimeout(() => {
                selectedElement.classList.remove('date-selected-animation');
            }, 600);
        }
    }

    showDateDetails(date, dateKey) {
        const detailsContainer = document.getElementById('selected-date-info');
        if (detailsContainer) {
            let html = this.formatSelectedDate(date);

            // Add event information if exists
            if (this.calendarEvents[dateKey]) {
                html += `<br><small class="text-info"><i class="mdi mdi-calendar-check me-1"></i>${this.calendarEvents[dateKey]}</small>`;
            }

            // Add day-specific information
            const dayOfWeek = date.getDay();
            if (dayOfWeek === 5 || dayOfWeek === 6) {
                html += `<br><small class="text-warning"><i class="mdi mdi-calendar-weekend me-1"></i>Weekend</small>`;
            }

            detailsContainer.innerHTML = html;
        }
    }

    goToToday() {
        const today = new Date();
        this.currentCalendarMonth = today.getMonth();
        this.currentCalendarYear = today.getFullYear();
        this.selectedDate = today;
        this.generateCalendar(this.currentCalendarMonth, this.currentCalendarYear);
    }

    addEvent() {
        if (!this.selectedDate) {
            alert('Please select a date first');
            return;
        }

        const eventText = prompt('Enter event description:', '');
        if (eventText && eventText.trim()) {
            const dateKey = `${this.selectedDate.getFullYear()}-${this.selectedDate.getMonth()}-${this.selectedDate.getDate()}`;
            this.calendarEvents[dateKey] = eventText.trim();
            this.generateCalendar(this.currentCalendarMonth, this.currentCalendarYear);

            // Show success feedback
            this.showCalendarNotification('Event added successfully!', 'success');
        }
    }

    viewCalendarMode() {
        // Toggle between different view modes (could be expanded)
        const modes = ['month', 'week', 'day'];
        const currentMode = document.querySelector('.calendar-grid').classList.contains('week-view') ? 'week' : 'month';

        // For now, just show info
        this.showCalendarNotification(`Current view: Month View`, 'info');
    }

    showCalendarNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `calendar-notification ${type}`;
        notification.innerHTML = `
            <i class="mdi mdi-information me-2"></i>
            ${message}
        `;

        const calendarWidget = document.getElementById('calendar-widget');
        if (calendarWidget) {
            calendarWidget.appendChild(notification);

            setTimeout(() => {
                notification.classList.add('show');
            }, 100);

            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }
    }

    // Initialize some sample events
    initializeCalendarEvents() {
        const today = new Date();
        const year = today.getFullYear();
        const month = today.getMonth();

        // Add some sample events
        this.calendarEvents[`${year}-${month}-${today.getDate()}`] = "Today's Schedule";
        this.calendarEvents[`${year}-${month}-${today.getDate() + 1}`] = "Meeting at 2 PM";
        this.calendarEvents[`${year}-${month}-${today.getDate() + 7}`] = "Weekly Review";
    }

    renderCalendar() {
        const year = this.currentDate.getFullYear();
        const month = this.currentDate.getMonth();
        
        // Update header - check if elements exist
        const monthElement = document.getElementById('current-month');
        const yearElement = document.getElementById('current-year');
        
        if (monthElement) {
            monthElement.textContent = this.currentDate.toLocaleDateString('en-US', { month: 'long' });
        }
        if (yearElement) {
            yearElement.textContent = year;
        }

        // Calculate calendar days
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const startDate = new Date(firstDay);
        startDate.setDate(startDate.getDate() - firstDay.getDay());

        const calendarGrid = document.getElementById('calendar-grid');
        if (!calendarGrid) return;

        let html = '';
        
        // Add day headers
        const dayHeaders = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        dayHeaders.forEach((day, index) => {
            const isWeekend = index === 0 || index === 6;
            html += `<div class="calendar-day-header ${isWeekend ? 'weekend-header' : ''}">${day}</div>`;
        });

        // Add calendar days
        const today = new Date();
        for (let i = 0; i < 42; i++) {
            const currentDate = new Date(startDate);
            currentDate.setDate(startDate.getDate() + i);
            
            const isCurrentMonth = currentDate.getMonth() === month;
            const isToday = this.isSameDate(currentDate, today);
            const isSelected = this.selectedDate && this.isSameDate(currentDate, this.selectedDate);
            const isWeekend = currentDate.getDay() === 0 || currentDate.getDay() === 6;
            const hasEvent = this.calendarData[this.formatDateKey(currentDate)];

            let classes = ['calendar-day'];
            if (!isCurrentMonth) classes.push('other-month');
            if (isToday) classes.push('today');
            if (isSelected) classes.push('selected');
            if (isWeekend) classes.push('weekend');
            if (hasEvent) classes.push('has-event');

            html += `
                <div class="${classes.join(' ')}" data-date="${this.formatDateKey(currentDate)}">
                    ${currentDate.getDate()}
                    ${isToday ? '<div class="today-indicator"></div>' : ''}
                    ${hasEvent ? '<div class="event-indicator"></div>' : ''}
                </div>
            `;
        }

        calendarGrid.innerHTML = html;
        calendarGrid.classList.add('calendar-updated');
        setTimeout(() => calendarGrid.classList.remove('calendar-updated'), 300);
    }

    navigateCalendar(direction) {
        this.currentDate.setMonth(this.currentDate.getMonth() + direction);
        
        const calendarGrid = document.getElementById('calendar-grid');
        if (calendarGrid) {
            calendarGrid.classList.add('month-transition');
            setTimeout(() => {
                this.renderCalendar();
                calendarGrid.classList.remove('month-transition');
            }, 150);
        }
    }

    selectCalendarDate(element) {
        const dateKey = element.dataset.date;
        this.selectedDate = new Date(dateKey);
        
        // Remove previous selection
        document.querySelectorAll('.calendar-day.selected').forEach(day => {
            day.classList.remove('selected');
        });
        
        // Add selection to clicked date
        element.classList.add('selected', 'date-selected-animation');
        setTimeout(() => element.classList.remove('date-selected-animation'), 600);
        
        // Update selected date info
        const dateInfo = document.getElementById('selected-date-info');
        if (dateInfo) {
            dateInfo.textContent = `Selected: ${this.selectedDate.toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            })}`;
        }
        
        this.showNotification(`Selected ${this.selectedDate.toLocaleDateString()}`, 'info');
        this.loadCalendarEvents(this.selectedDate);
    }

    handleCalendarControl(action) {
        const today = new Date();
        
        switch (action) {
            case 'today':
                this.currentDate = new Date(today);
                this.selectedDate = new Date(today);
                break;
            case 'clear':
                this.selectedDate = null;
                document.getElementById('selected-date-info').textContent = 'No date selected';
                break;
        }
        
        this.renderCalendar();
    }

    async loadCalendarEvents(date = null) {
        try {
            const params = new URLSearchParams();
            if (date) {
                params.append('date', this.formatDateKey(date));
            } else {
                params.append('month', this.currentDate.getMonth() + 1);
                params.append('year', this.currentDate.getFullYear());
            }

            const response = await fetch(`/admin/calendar/events?${params.toString()}`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                }
            });

            if (response.ok) {
                const events = await response.json();
                this.calendarData = events;
                if (!date) this.renderCalendar();
            } else {
                console.warn('Calendar events endpoint not available, using empty calendar');
                this.calendarData = {};
                if (!date) this.renderCalendar();
            }
        } catch (error) {
            console.warn('Calendar events not available:', error.message);
            this.calendarData = {};
            if (!date) this.renderCalendar();
            console.error('Error loading calendar events:', error);
        }
    }

    // Clock Management
    initializeClock() {
        const canvas = document.getElementById('clockCanvas');
        if (!canvas) return;

        this.clockCanvas = canvas;
        this.clockCtx = canvas.getContext('2d');
        
        // Set canvas size
        canvas.width = 120;
        canvas.height = 120;
        
        // Initialize canvas clock with proper translation
        this.radius = canvas.height / 2;
        this.clockCtx.translate(this.radius, this.radius);
        this.radius = this.radius * 0.90;
        
        this.updateClock();
        this.clockInterval = setInterval(() => this.updateClock(), 1000);
    }

    updateClock() {
        if (!this.clockCtx || !this.clockCanvas) return;

        this.drawClock();
    }

    drawClock() {
        if (!this.clockCtx || !this.clockCanvas) {
            return;
        }

        const ctx = this.clockCtx;
        const radius = this.radius;

        // Clear the entire canvas
        ctx.clearRect(-radius * 1.1, -radius * 1.1, radius * 2.2, radius * 2.2);

        // Draw clock components
        this.drawFace(ctx, radius);
        this.drawNumbers(ctx, radius);
        this.drawTime(ctx, radius);
    }

    drawFace(ctx, radius) {
        const grad = ctx.createRadialGradient(0, 0, radius * 0.95, 0, 0, radius * 1.05);
        grad.addColorStop(0, '#0c335aff');
        grad.addColorStop(0.5, 'black');
        grad.addColorStop(1, '#00070eff');

        ctx.beginPath();
        ctx.arc(0, 0, radius, 0, 2 * Math.PI);
        ctx.fillStyle = '#01162cff';
        ctx.fill();
        ctx.strokeStyle = grad;
        ctx.lineWidth = radius * 0.1;
        ctx.stroke();

        // Center dot
        ctx.beginPath();
        ctx.arc(0, 0, radius * 0.1, 0, 2 * Math.PI);
        ctx.fillStyle = '#333';
        ctx.fill();
    }

    drawNumbers(ctx, radius) {
        ctx.font = radius * 0.2 + "px Arial, sans-serif";
        ctx.textBaseline = "middle";
        ctx.textAlign = "center";
        ctx.fillStyle = "#ffffffff";

        for (let num = 1; num <= 12; num++) {
            // Calculate angle: start from 12 o'clock (top) and go clockwise
            // 12 o'clock is at -90 degrees (-Math.PI/2), then add for each hour
            let ang = (num - 3) * Math.PI / 6; // -3 to start at 12 o'clock position

            // Calculate position for the number
            let x = Math.cos(ang) * radius * 0.85;
            let y = Math.sin(ang) * radius * 0.85;

            // Draw the number at calculated position
            ctx.fillText(num.toString(), x, y);
        }
    }

    drawTime(ctx, radius){
        const now = new Date();
        let hour = now.getHours();
        let minute = now.getMinutes();
        let second = now.getSeconds();

        //hour
        hour = hour % 12;
        hour = (hour * Math.PI / 6) +
               (minute * Math.PI / (6 * 60)) +
               (second * Math.PI / (360 * 60));
        this.drawHand(ctx, hour, radius * 0.5, radius * 0.07, '#39ffdeff');

        //minute
        minute = (minute * Math.PI / 30) + (second * Math.PI / (30 * 60));
        this.drawHand(ctx, minute, radius * 0.8, radius * 0.05, '#45ff70ff');

        // second
        second = (second * Math.PI / 30);
        this.drawHand(ctx, second, radius * 0.9, radius * 0.02, '#e70017ff');
    }

    drawHand(ctx, pos, length, width, color) {
        ctx.beginPath();
        ctx.lineWidth = width;
        ctx.lineCap = "round";
        ctx.strokeStyle = color;
        ctx.moveTo(0, 0);
        ctx.rotate(pos);
        ctx.lineTo(0, -length);
        ctx.stroke();
        ctx.rotate(-pos);
    }

    // Chart Management
    initializeCharts() {
        // Check if Chart.js is available
        if (typeof Chart === 'undefined') {
            console.warn('Chart.js not loaded, skipping chart initialization');
            return;
        }
        
        try {
            this.initializeSalesChart();
        } catch (error) {
            console.warn('Sales chart initialization failed:', error);
        }
        
        try {
            this.initializeEarningsChart();
        } catch (error) {
            console.warn('Earnings chart initialization failed:', error);
        }
        
        try {
            this.initializeOrdersChart();
        } catch (error) {
            console.warn('Orders chart initialization failed:', error);
        }
        
        try {
            this.initializeCustomersChart();
        } catch (error) {
            console.warn('Customers chart initialization failed:', error);
        }
    }

    initializeSalesChart() {
        const ctx = document.getElementById('salesChart');
        if (!ctx) return;

        this.charts.sales = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Loading...'],
                datasets: [{
                    label: 'Sales',
                    data: [0],
                    borderColor: 'rgba(255, 255, 255, 0.8)',
                    backgroundColor: 'rgba(255, 255, 255, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            color: 'white'
                        }
                    }
                },
                scales: {
                    y: {
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.7)'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.2)'
                        }
                    },
                    x: {
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.7)'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.2)'
                        }
                    }
                }
            }
        });
    }

    initializeEarningsChart() {
        const ctx = document.getElementById('earningsChart');
        if (!ctx) return;

        this.charts.earnings = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Loading...'],
                datasets: [{
                    label: 'Earnings',
                    data: [0],
                    backgroundColor: 'rgba(255, 255, 255, 0.3)',
                    borderColor: 'rgba(255, 255, 255, 0.8)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            color: 'white'
                        }
                    }
                },
                scales: {
                    y: {
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.7)'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.2)'
                        }
                    },
                    x: {
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.7)'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.2)'
                        }
                    }
                }
            }
        });
    }

    initializeOrdersChart() {
        const ctx = document.getElementById('ordersChart');
        if (!ctx) return;

        this.charts.orders = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Loading...'],
                datasets: [{
                    data: [1],
                    backgroundColor: [
                        'rgba(255, 255, 255, 0.3)'
                    ],
                    borderColor: [
                        'rgba(255, 255, 255, 0.8)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: 'white',
                            usePointStyle: true,
                            padding: 20
                        }
                    }
                }
            }
        });
    }

    initializeCustomersChart() {
        const ctx = document.getElementById('customersChart');
        if (!ctx) return;

        this.charts.customers = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Loading...'],
                datasets: [{
                    label: 'New Customers',
                    data: [0],
                    borderColor: 'rgba(255, 255, 255, 0.8)',
                    backgroundColor: 'rgba(255, 255, 255, 0.2)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            color: 'white'
                        }
                    }
                },
                scales: {
                    y: {
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.7)'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.2)'
                        }
                    },
                    x: {
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.7)'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.2)'
                        }
                    }
                }
            }
        });
    }

    updateCharts(data) {
        console.log('🔥 UPDATING CHARTS WITH DYNAMIC DATA:');
        console.log('Charts data received:', data);
        
        // Update Sales Chart (line chart)
        if (data.salesChart && this.charts.sales) {
            console.log('📈 Updating Sales Chart:');
            console.log('  Labels:', data.salesChart.labels);
            console.log('  Data:', data.salesChart.datasets?.[0]?.data);
            
            this.charts.sales.data.labels = data.salesChart.labels || ['No Data'];
            this.charts.sales.data.datasets[0].data = data.salesChart.datasets?.[0]?.data || [0];
            this.charts.sales.data.datasets[0].label = data.salesChart.datasets?.[0]?.label || 'Sales';
            this.charts.sales.update('active');
            console.log('✅ Sales chart updated successfully');
        } else {
            console.warn('❌ Sales chart data missing or chart not initialized');
        }
        
        // Update Earnings Chart (bar chart)  
        if (data.earningsChart && this.charts.earnings) {
            console.log('💰 Updating Earnings Chart:');
            console.log('  Labels:', data.earningsChart.labels);
            console.log('  Data:', data.earningsChart.datasets?.[0]?.data);
            
            this.charts.earnings.data.labels = data.earningsChart.labels || ['No Data'];
            this.charts.earnings.data.datasets[0].data = data.earningsChart.datasets?.[0]?.data || [0];
            this.charts.earnings.data.datasets[0].label = data.earningsChart.datasets?.[0]?.label || 'Earnings';
            this.charts.earnings.update('active');
            console.log('✅ Earnings chart updated successfully');
        } else {
            console.warn('❌ Earnings chart data missing or chart not initialized');
        }
        
        // Update Orders Chart (doughnut chart)
        if (data.ordersChart && this.charts.orders) {
            console.log('📋 Updating Orders Chart:');
            console.log('  Labels:', data.ordersChart.labels);
            console.log('  Data:', data.ordersChart.datasets?.[0]?.data);
            
            this.charts.orders.data.labels = data.ordersChart.labels || ['No Data'];
            this.charts.orders.data.datasets[0].data = data.ordersChart.datasets?.[0]?.data || [1];
            
            // Update colors for orders chart based on data
            if (data.ordersChart.datasets?.[0]?.data && data.ordersChart.datasets[0].data.length > 1) {
                this.charts.orders.data.datasets[0].backgroundColor = [
                    'rgba(3, 70, 12, 0.82)',      // Completed - Green
                    'rgba(255, 232, 22, 1)',       // Pending - Yellow
                    'rgba(252, 16, 16, 0.82)'      // Cancelled - Red
                ];
                this.charts.orders.data.datasets[0].borderColor = [
                    'rgba(255, 255, 255, 0.8)',
                    'rgba(255, 255, 255, 0.6)',
                    'rgba(255, 255, 255, 0.4)'
                ];
            } else {
                // Fallback for loading state
                this.charts.orders.data.datasets[0].backgroundColor = ['rgba(255, 255, 255, 0.3)'];
                this.charts.orders.data.datasets[0].borderColor = ['rgba(255, 255, 255, 0.8)'];
            }
            
            this.charts.orders.update('active');
            console.log('✅ Orders chart updated successfully');
        } else {
            console.warn('❌ Orders chart data missing or chart not initialized');
        }
        
        // Update Customers Chart (line chart)
        if (data.customersChart && this.charts.customers) {
            console.log('👥 Updating Customers Chart:');
            console.log('  Labels:', data.customersChart.labels);
            console.log('  Data:', data.customersChart.datasets?.[0]?.data);
            
            this.charts.customers.data.labels = data.customersChart.labels || ['No Data'];
            this.charts.customers.data.datasets[0].data = data.customersChart.datasets?.[0]?.data || [0];
            this.charts.customers.data.datasets[0].label = data.customersChart.datasets?.[0]?.label || 'New Customers';
            this.charts.customers.update('active');
            console.log('✅ Customers chart updated successfully');
        } else {
            console.warn('❌ Customers chart data missing or chart not initialized');
        }

        // Add chart update animation
        Object.values(this.charts).forEach(chart => {
            if (chart && chart.canvas) {
                chart.canvas.parentElement.classList.add('chart-updated');
                setTimeout(() => {
                    chart.canvas.parentElement.classList.remove('chart-updated');
                }, 500);
            }
        });
        
        console.log('🎯 All charts update process completed');
    }

    // Utility Methods
    isSameDate(date1, date2) {
        return date1.getFullYear() === date2.getFullYear() &&
               date1.getMonth() === date2.getMonth() &&
               date1.getDate() === date2.getDate();
    }

    formatDateKey(date) {
        return date.toISOString().split('T')[0];
    }

    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `calendar-notification ${type}`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => notification.classList.add('show'), 100);
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => document.body.removeChild(notification), 300);
        }, 3000);
    }

    // Cleanup
    destroy() {
        if (this.clockInterval) {
            clearInterval(this.clockInterval);
        }
        
        Object.values(this.charts).forEach(chart => {
            if (chart) chart.destroy();
        });
    }
}

// Global function for quick range buttons (called from blade template)
window.setQuickRange = function(days) {
    if (window.dashboardManager) {
        window.dashboardManager.handleQuickSelect(days);
    }
};

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (window.dashboardManager) {
        window.dashboardManager.destroy();
    }
});
