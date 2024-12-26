<style>
	* {
		box-sizing: border-box;
	}

	.icalendar {
		background-color: #f7f7f7;
		border: 2px solid white;
		-webkit-box-shadow: 0 0 10px 0 rgba(0, 0, 0, .1);
		box-shadow: 0 0 10px 0 rgba(0, 0, 0, .1);
		min-width: 100%;
		max-width: 100%;
		margin-top: 20px;
		margin-bottom: 20px;
	}

	.icalendar__month {
		display: -webkit-box;
		display: -ms-flexbox;
		display: flex;
		-webkit-box-pack: justify;
		-ms-flex-pack: justify;
		justify-content: space-between;
		-webkit-box-align: center;
		-ms-flex-align: center;
		align-items: center;
		background-color: #15a0a3cf;
		width: 100%;
		padding: 10px;
		color: #fff;
		text-align: center
	}

	.icalendar__days, .icalendar__week-days {
		display: -webkit-box;
		display: -ms-flexbox;
		display: flex;
		-ms-flex-wrap: wrap;
		flex-wrap: wrap
	}

	.icalendar__week-days div {
		border-left: 1px solid rgba(255, 255, 255, 0.3);
		text-transform: uppercase;
		font-weight: 500;
		font-size: 11px;
		letter-spacing: 1px;
		text-align: center;
		color: rgba(255, 255, 255, 0.9)
	}

	.icalendar__week-days {
		background-color: #15a0a3;
		padding: 7px 0;
		color: #fff
	}

	.icalendar__days label, .icalendar__days div, .icalendar__week-days div {
		width: 14.28%;
		text-align: center
	}

	.icalendar__days label {
		display: block;
		position: relative;
		padding: 4px 0;
	}

	.icalendar__days label.chosen_date {
		background-color: #2ecc71;
		box-shadow: 0 0 0 0.3px rgba(255, 255, 255, 0.97);
		color: #FFF;
	}

	.icalendar__days input {
		position: absolute;
		top: 0;
		left: 0;
		display: flex;
		justify-content: center;
		align-items: center;
		width: 100%;
		height: 100%;
		visibility: hidden;
	}

	.icalendar__days div {
		padding: 4px 0;
		-webkit-transition: .3s;
		transition: .3s
	}

	.icalendar__days div:hover {
		background-color: #dfe6e9;
		cursor: pointer
	}

	.icalendar__today {
		background-color: #15a0a3;
		box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.9);
		color: white;
	}

	.icalendar__next-date, .icalendar__prev-date {
		color: #bbb
	}

	div.icalendar__next-date:hover, div.icalendar__prev-date:hover {
		/* background-color: transparent; */
		/* cursor: text */
        background-color: #dfe6e9;
        cursor: pointer;
	}

	.icalendar__current-date #icalendarMonth {
		margin-bottom: 0;
		padding: 0;
		font-size: 16px;
		color: white;
		font-weight: 700;
		text-transform: uppercase;
	}

	#icalendarDateStr {
		font-size: 13px;
		color: rgba(255, 255, 255, .9);
		text-transform: uppercase;
	}

	.icalendar__next, .icalendar__prev {
		display: -webkit-box;
		display: -ms-flexbox;
		display: flex;
		-webkit-box-pack: center;
		-ms-flex-pack: center;
		justify-content: center;
		-webkit-box-align: center;
		-ms-flex-align: center;
		align-items: center;
		background-color: rgba(0, 0, 0, .1);
		border-radius: 50%;
		width: 30px;
		height: 30px;
		font-size: 14px;
		line-height: 1;
		-webkit-transition: .3s;
		transition: .3s;
		cursor: pointer
	}

	.icalendar__next:hover, .icalendar__prev:hover {
		background-color: rgba(0, 0, 0, .2)
	}

	.fix-departure-date-span {
		position: relative;
		display: inline-block;
		padding: 6px 16px 6px 6px;
		background-color: white;
		border: 1px solid rgba(0, 0, 0, 0.2);
		margin: 4px;
		border-radius: 4px;
		line-height: 1;
		font-size: 12px;
		letter-spacing: 1px;
	}

	.fix-departure-date-span .ddt-fix-departure-calendar-date-remove {
		position: absolute;
		top: 0;
		right: 0;
		padding: 2px;
		width: 16px;
		height: 16px;
		color: rgba(0, 0, 0, 0.7);
		font-size: 10px;
		text-decoration: none;
		text-align: center;
	}

	.fix-departure-date-span .ddt-fix-departure-calendar-date-remove:hover {
		color: red;
	}

	textarea.meta-fix-departure-calendar {
		width: 100%;
	}

	.ddt-fix-departure-calendar-box {
		display: flex;
		flex-wrap: wrap;
	}

	.ddt-fix-departure-calendar-box .ddt-fix-departure-calendar-box__column {
		width: 270px;
	}

	.ddt-fix-departure-calendar-box .ddt-fix-departure-calendar-box__column + .ddt-fix-departure-calendar-box__column {
		width: calc(100% - 275px);
		padding: 40px 10px;
	}

    .icalendar__indicator {
        display: inline-block;
        width: 30px;
        height: 30px;
        line-height: 30px;
        border-radius: 50%;
        background-color: red;
        color: white;
        text-align: center;
        cursor: pointer
    }

	@media all and (max-width: 540px) {
		.ddt-fix-departure-calendar-box .ddt-fix-departure-calendar-box__column + .ddt-fix-departure-calendar-box__column {
			width: 100%;
			background-color: rgba(0, 0, 0, 0.01);
			padding-top: 20px;
			padding-bottom: 20px;
		}
	}
</style>