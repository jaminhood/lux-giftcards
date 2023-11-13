import { formatDistanceToNow, parseISO } from "date-fns"

export const pastDate = time => {
	let newTime = ``
	if (time) {
		const newDate = parseISO(time)
		const timePeriod = formatDistanceToNow(newDate)
		newTime = `${timePeriod} ago`
	}
	return newTime
}
