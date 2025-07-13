<?php

namespace App\Enums;

enum AnalyticsTagNames: string
{
    case TotalMeetings = 'total_meetings';
    case AverageResponsesPerMeeting = 'avg_responses_per_meeting';
    case MaxResponsesOnMeetings = 'max_responses_on_meetings';
}
