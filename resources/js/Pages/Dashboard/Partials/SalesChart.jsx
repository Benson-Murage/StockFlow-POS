import * as React from "react"
import { Area, AreaChart, CartesianGrid, XAxis, YAxis } from "recharts"

import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "@/Components/ui/card"
import {
  ChartContainer,
  ChartLegend,
  ChartLegendContent,
  ChartTooltip,
  ChartTooltipContent,
} from "@/Components/ui/chart"
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/Components/ui/select"
import { usePage } from "@inertiajs/react"
import { useCurrencyFormatter } from "@/lib/currencyFormatter"

export const description = "An interactive area chart"

const chartConfig = {
  visitors: {
    label: "Sales",
  },
  sale: {
    label: "Sales",
    color: "var(--chart-2)",
  },
  cash: {
    label: "Cash",
    color: "var(--chart-4)",
  },
  profit: {
    label: "Profit",
    color: "var(--chart-3)",
  },
}

export function SalesChart() {
  const chartData = usePage().props.data.saleChart || []
  const formatCurrency = useCurrencyFormatter()

  const [timeRange, setTimeRange] = React.useState("90d")

  const filteredData = chartData.filter((item) => {
    const date = new Date(item.date)
    const referenceDate = new Date()
    let daysToSubtract = 90
    if (timeRange === "30d") {
      daysToSubtract = 30
    } else if (timeRange === "7d") {
      daysToSubtract = 7
    }
    const startDate = new Date(referenceDate)
    startDate.setDate(startDate.getDate() - daysToSubtract)
    return date >= startDate
  })

  const rangeLabel =
    timeRange === "7d" ? "Last 7 days" : timeRange === "30d" ? "Last 30 days" : "Last 3 months"

  const totals = React.useMemo(() => {
    return filteredData.reduce(
      (acc, item) => {
        acc.sale += Number(item.sale || 0)
        acc.cash += Number(item.cash || 0)
        acc.profit += Number(item.profit || 0)
        return acc
      },
      { sale: 0, cash: 0, profit: 0 }
    )
  }, [filteredData])

  if (!chartData || chartData.length === 0) {
    return (
      <Card className="pt-0 w-full h-full">
        <CardHeader className="flex items-center gap-2 space-y-0 border-b py-5 sm:flex-row">
          <div className="grid flex-1 gap-1">
            <CardTitle>SALES</CardTitle>
            <CardDescription>
              Showing total sales for the last 3 months
            </CardDescription>
          </div>
        </CardHeader>
        <CardContent className="px-2 pt-4 sm:px-6 sm:pt-6 flex items-center justify-center h-[250px]">
          <p className="text-muted-foreground">No data available</p>
        </CardContent>
      </Card>
    )
  }

  return (
    <Card className="pt-0 w-full h-full">
      <CardHeader className="flex items-center gap-2 space-y-0 border-b py-5 sm:flex-row">
        <div className="grid flex-1 gap-1">
          <CardTitle>SALES</CardTitle>
          <CardDescription>
            {rangeLabel} • Total {formatCurrency(totals.sale)}
          </CardDescription>
        </div>
        <Select value={timeRange} onValueChange={setTimeRange}>
          <SelectTrigger
            className="hidden w-[160px] rounded-lg sm:ml-auto sm:flex"
            aria-label="Select a value"
          >
            <SelectValue placeholder="Last 3 months" />
          </SelectTrigger>
          <SelectContent className="rounded-xl bg-white">
            <SelectItem value="90d" className="rounded-lg">
              Last 3 months
            </SelectItem>
            <SelectItem value="30d" className="rounded-lg">
              Last 30 days
            </SelectItem>
            <SelectItem value="7d" className="rounded-lg">
              Last 7 days
            </SelectItem>
          </SelectContent>
        </Select>
      </CardHeader>
      <CardContent className="px-2 pt-4 sm:px-6 sm:pt-6">
        <ChartContainer
          config={chartConfig}
          className="aspect-auto h-[250px] w-full min-h-[200px] min-w-[300px]"
        >
          <AreaChart data={filteredData}>
            <defs>
              <linearGradient id="fillCash" x1="0" y1="0" x2="0" y2="1">
                <stop
                  offset="5%"
                  stopColor="var(--color-cash)"
                  stopOpacity={0.8}
                />
                <stop
                  offset="95%"
                  stopColor="var(--color-cash)"
                  stopOpacity={0.1}
                />
              </linearGradient>
              <linearGradient id="fillSale" x1="0" y1="0" x2="0" y2="1">
                <stop
                  offset="5%"
                  stopColor="var(--color-sale)"
                  stopOpacity={0.8}
                />
                <stop
                  offset="95%"
                  stopColor="var(--color-sale)"
                  stopOpacity={0.1}
                />
              </linearGradient>
              <linearGradient id="fillProfit" x1="0" y1="0" x2="0" y2="1">
                <stop
                  offset="5%"
                  stopColor="var(--color-profit)"
                  stopOpacity={0.8}
                />
                <stop
                  offset="95%"
                  stopColor="var(--color-profit)"
                  stopOpacity={0.1}
                />
              </linearGradient>
            </defs>
            <CartesianGrid vertical={false} />
            <XAxis
              dataKey="date"
              tickLine={false}
              axisLine={false}
              tickMargin={8}
              minTickGap={32}
              tickFormatter={(value) => {
                const date = new Date(value)
                return date.toLocaleDateString("en-US", {
                  month: "short",
                  day: "numeric",
                })
              }}
            />
            {/* <YAxis
              tickLine={false}
              axisLine={false}
              tickMargin={8}
              tickCount={2}
            /> */}
            <ChartTooltip
              cursor={false}
              content={
                <ChartTooltipContent
                className="bg-white"
                  labelFormatter={(value) => {
                    return new Date(value).toLocaleDateString("en-US", {
                      month: "short",
                      day: "numeric",
                    })
                  }}
                  formatter={(value, name) => {
                    // Keep the same label, but render amounts as currency
                    return (
                      <div className="flex w-full justify-between gap-6">
                        <span className="text-muted-foreground">{name}</span>
                        <span className="font-mono font-medium tabular-nums text-foreground">
                          {formatCurrency(Number(value || 0))}
                        </span>
                      </div>
                    )
                  }}
                  indicator="dot"
                />
              }
            />
            <Area
              dataKey="sale"
              type="natural"
              fill="url(#fillSale)"
              stroke="var(--color-sale)"
              stackId="a"
            />
            <Area
              dataKey="cash"
              type="natural"
              fill="url(#fillCash)"
              stroke="var(--color-cash)"
              stackId="a"
            />
            <Area
              dataKey="profit"
              type="natural"
              fill="url(#fillProfit)"
              stroke="var(--color-profit)"
              stackId="b"
            />
            <ChartLegend content={<ChartLegendContent />} />
          </AreaChart>
        </ChartContainer>
      </CardContent>
    </Card>
  )
}
