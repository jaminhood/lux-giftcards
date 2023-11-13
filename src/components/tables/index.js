import React from "react"
import HomeTable from "./HomeTable"

const Tables = ({ page }) => {
	return <>{page === `home` && <HomeTable />}</>
}

export default Tables
