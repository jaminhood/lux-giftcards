import React from "react"
import ReactDOM from "react-dom"
import "./index.css"
import Home from "./pages/Home"

document.addEventListener("DOMContentLoaded", async function () {
	const url = window.location.href
	const nameFromURL = url.split(`/`)
	nameFromURL.pop()
	const page_name = nameFromURL.pop()

	if (document.querySelector("#lux-giftcard-home")) {
		ReactDOM.render(<Home />, document.querySelector("#lux-giftcard-home"))
	}
})
