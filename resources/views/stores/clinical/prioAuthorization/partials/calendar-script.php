<script>
    "use strict";
    let dt = new Date();
   
    function renderDate() {
        let dateString = new Date();

        dt.setDate(1);
        let day = dt.getDay();

        let endDate = new Date(dt.getFullYear(), dt.getMonth() + 1, 0).getDate();

        let prevDate = new Date(dt.getFullYear(), dt.getMonth(), 0).getDate();
        
        let today = new Date();

        let months = [
            "January",
            "February",
            "March",
            "April",
            "May",
            "June",
            "July",
            "August",
            "September",
            "October",
            "November",
            "December",
        ];

        document.getElementById("icalendarMonth").innerHTML =
            months[dt.getMonth()] + " , " + dt.getFullYear();
        document.getElementById("icalendarDateStr").innerHTML =
            dateString.toDateString();

        fetchClinicalKpiData(dt).then(response => {
            let clinicalKpiDates = response.dates;
            let clinicalKpiData = response.data;
            let cells = "";
            let countDate = 0;

            for (let x = day; x > 0; x--) {
                let prevDateString = new Date(dt.getFullYear(), dt.getMonth() - 1, prevDate - x + 1);
                let formattedPrevDate = formatDate(prevDateString.toISOString().split('T')[0], false);
                const dayKpiData = clinicalKpiData.filter(item => item.date === formattedPrevDate);

                let kpiIndicator = dayKpiData.length > 0 ? `<span class='icalendar__indicator' data-bs-html="true" data-bs-original-title="${formattedPrevDate}" data-bs-content="${prepareKpiContent(dayKpiData)}">${prevDate - x + 1}</span>` : (prevDate - x + 1);
                cells += `<div class='icalendar__prev-date' id='${formattedPrevDate}' data-date='${formattedPrevDate}'>${kpiIndicator}</div>`;
            }

            for (let i = 1; i <= endDate; i++) {
                let dateString = dt.getFullYear() + '-' + (dt.getMonth() + 1) + '-' + i;
                let formattedDate = formatDate(dateString, true);
                const dayKpiData = clinicalKpiData.filter(item => item.date === formattedDate);

                let kpiIndicator = dayKpiData.length > 0 ? `<span data-bs-html="true" data-bs-original-title="${formattedDate}" data-bs-content="${prepareKpiContent(dayKpiData)}" class='icalendar__indicator'>${i}</span>` : i;

                if (i === today.getDate() && dt.getMonth() === today.getMonth() && dt.getFullYear() === today.getFullYear()) {
                    cells += `<div class='icalendar__today' id='${formattedDate}' data-date='${formattedDate}'>${kpiIndicator}</div>`;
                } else {
                    cells += `<div class='icalendar__date' id='${formattedDate}' data-date='${formattedDate}'>${kpiIndicator}</div>`;
                }

                countDate = i;
            }

            let reservedDateCells = countDate + day + 1;
            for (let j1 = reservedDateCells, j2 = 1; j1 <= 42; j1++, j2++) {
                let nextDateString = new Date(dt.getFullYear(), dt.getMonth() + 1, j2);
                let formattedNextDate = formatDate(nextDateString.toISOString().split('T')[0], false);
                const dayKpiData = clinicalKpiData.filter(item => item.date === formattedNextDate);

                let kpiIndicator = dayKpiData.length > 0 ? `<span class='icalendar__indicator' data-bs-html="true" data-bs-original-title="${formattedNextDate}" data-bs-content="${prepareKpiContent(dayKpiData)}">${j2}</span>` : j2;
                cells += `<div class='icalendar__next-date' id='${formattedNextDate}' data-date='${formattedNextDate}'>${kpiIndicator}</div>`;
            }


            document.getElementsByClassName("icalendar__days")[0].innerHTML = cells;

            // Add event listeners for hover and click
            document.querySelectorAll('.icalendar__indicator').forEach(element => {
                element.parentElement.addEventListener('click', function() {
                    const date = this.getAttribute('data-date');
                    // showModal(date, clinicalKpiData);
                });
            });
            bindPopoverEvents();
        });
    }

    function prepareKpiContent(kpiData) {
        const listItems = kpiData.map(data => {
            return `<li>${data.dFirstname} ${data.dLastname}</li>`; // Wrap each name in a list item
        }).join(''); // Join list items without additional separators

        return `<ul>${listItems}</ul>`; 
    }

    function bindPopoverEvents() {
        $('.icalendar__indicator').off('mouseenter mouseleave').on('mouseenter', function() {
            // alert('hey');
            $(this).popover('show');
        }).on('mouseleave', function() {
            $(this).popover('hide');
        });
        // $(function () {
        //     $('[data-bs-toggle="popover"]').popover({
        //         container: 'body',
        //         html: true
        //     });
        // });
    }


    renderDate();

    function moveDate(param) {
        if (param === "prev") {
            dt.setMonth(dt.getMonth() - 1);
        } else if (param === "next") {
            dt.setMonth(dt.getMonth() + 1);
        }

        renderDate();
    }

    function formatDate(date, thisMonth) {
        if(thisMonth){
            const parts = date.split('-');
            const month = parts[1].length === 1 ? '0' + parts[1] : parts[1];
            const day = parts[2].length === 1 ? '0' + parts[2] : parts[2];
            const year = parts[0];
            return year + '-' + month + '-' + day;
        }
        else{
            const parts = date.split('-');
            const year = parseInt(parts[0], 10);
            const month = parseInt(parts[1], 10) - 1;
            const day = parseInt(parts[2], 10);
            let currentDate = new Date(year, month, day);

            // Add 1 day to the current date
            currentDate.setDate(currentDate.getDate() + 1);

            // Get the year, month, and day from the updated date
            const updatedYear = currentDate.getFullYear();
            const updatedMonth = currentDate.getMonth() + 1;
            const updatedDay = currentDate.getDate();

            // Format the updated date parts with leading zeros if necessary
            const formattedMonth = updatedMonth.toString().padStart(2, '0');
            const formattedDay = updatedDay.toString().padStart(2, '0');

            // Return the updated date string in the format 'YYYY-MM-DD'
            return `${updatedYear}-${formattedMonth}-${formattedDay}`;
        }
        
    }

    function fetchClinicalKpiData(currentDate) {
        console.log(currentDate);
        const prevMonth = new Date(currentDate.getFullYear(), currentDate.getMonth() - 1, 1);
        const nextMonth = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 1);

        const data = {
            prevMonthYear: prevMonth.getFullYear(),
            prevMonth: prevMonth.getMonth() + 1, // Month is zero-indexed, so add 1
            currentMonthYear: currentDate.getFullYear(),
            currentMonth: currentDate.getMonth() + 1,
            nextMonthYear: nextMonth.getFullYear(),
            nextMonth: nextMonth.getMonth() + 1,
        };

        return new Promise((resolve, reject) => {
            $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "GET",
            url: "/store/clinical/prio-authorization/date_with",
            data: data,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data) {
                    console.log(data);
                    // Assuming your API returns a list of dates
                    resolve(data); // Adjust according to your API response structure
                },
                error: function(err) {
                    handleErrorResponse(err);
                    console.error('Error fetching ClinicalOutreach data', err);
                    reject(err);
                }
            });
        });
    }

    function showModal(date, clinicalKpiData) {
    
        const matchingData = clinicalKpiData.filter(item => item.date === date);

        if (matchingData.length > 0) {
            // let contentHtml = '<ul>';
            // // Loop through all matching entries and format them
            // matchingData.forEach(data => {
            //     contentHtml += `<li>${JSON.stringify(data, null, 2)}</li>`;
            // });
            // contentHtml += '</ul>';

            // // Display formatted data in modal
            // $('#modalContent').html(contentHtml);

            // Clear any existing buttons
            $('#selection_modal #modalContent').empty();

            // Create buttons for each data entry
            matchingData.forEach(data => {
                const buttonHtml = `<button class="btn btn-sm btn-primary me-1 d-flex" id="data-calendar-edit-btn-${data.id}" data-id="${data.id}" data-array='${JSON.stringify(data)}' onclick="showEditForm(${data.id});"><i class="fa fa-pencil me-2"></i>${data.dFirstname} ${data.dLastname}</button>`;
                $('#selection_modal #modalContent').append(buttonHtml);
            });

            $('#selection_modal').modal('show');
        } else {
            // Optionally handle the case where no data is found
            $('#selection_modal #modalContent').html('<p>No data available for this date.</p>');
            $('#selection_modal').modal('show');
        }
    }

</script>