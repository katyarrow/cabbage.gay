<?php

namespace App\Services;

use App\Enums\AnalyticsTagNames;
use App\Models\AnalyticsEntry;
use App\Models\AnalyticsEntryTag;
use App\Models\AnalyticsTag;
use App\Models\Meeting;
use DB;

class AnalyticsService
{
    public function generateTotalMeetingsData() {
        DB::transaction(function() {
            $entry = new AnalyticsEntry();
            $entry->value = Meeting::count();
            $entry->save();

            $tag = new AnalyticsEntryTag();
            $tag->analytics_entry_id = $entry->id;
            $tag->analytics_tag_id = AnalyticsTag::getByName(AnalyticsTagNames::TotalMeetings)->id;
            $tag->save();
        });
    }

    public function generateAverageResponsesPerMeetingData() {
        DB::transaction(function() {
            $totalMeetings = Meeting::count();
            $sumOfAllResponses = Meeting::query()
                ->select(
                    DB::raw('(select count(*) from meeting_attendees where meeting_id = meetings.id) as response_count')
                )
                ->pluck('response_count')
                ->max();
            $avg = $sumOfAllResponses / $totalMeetings;
            $entry = new AnalyticsEntry();
            $entry->value = $avg;
            $entry->save();

            $tag = new AnalyticsEntryTag();
            $tag->analytics_entry_id = $entry->id;
            $tag->analytics_tag_id = AnalyticsTag::getByName(AnalyticsTagNames::AverageResponsesPerMeeting)->id;
            $tag->save();
        });
    }

    public function generateMaxResponsesOnMeetingData() {
        DB::transaction(function() {
            $maxResponseCount = Meeting::query()
                ->selectRaw('(select count(*) from meeting_attendees where meeting_id = meetings.id) as response_count')
                ->pluck('response_count')
                ->max();
            $entry = new AnalyticsEntry();
            $entry->value = $maxResponseCount;
            $entry->save();

            $tag = new AnalyticsEntryTag();
            $tag->analytics_entry_id = $entry->id;
            $tag->analytics_tag_id = AnalyticsTag::getByName(AnalyticsTagNames::AverageResponsesPerMeeting)->id;
            $tag->save();
        });
    }
}
